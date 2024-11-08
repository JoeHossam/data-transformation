<?php


namespace App\Services;

use App\Exceptions\TransformationException;
use App\Models\Claim;
use App\Models\MappingRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DataTransformerService
{
    protected Collection $schema;
    protected Claim $claim;

    public function transform(int $claimId, int $endpointId): array
    {
        $cacheKey = "transformed_data_{$claimId}_{$endpointId}";
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($claimId, $endpointId) {
            try {
                $this->claim = Claim::findOrFail($claimId);
                $this->schema = MappingRule::where('endpoint_id', $endpointId)->get();

                if ($this->schema->isEmpty()) {
                    throw new TransformationException("No schema found for endpoint {$endpointId}");
                }

                $dependencyTree = $this->buildDependencyTree();
                return $this->transformData($dependencyTree);

            } catch (\Exception $e) {
                Log::error('Data transformation failed', [
                    'claim_id' => $claimId,
                    'endpoint_id' => $endpointId,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    protected function buildDependencyTree(): Collection
    {
        $nodes = $this->schema->keyBy('id')->map(function ($rule) {
            return (object) [
                'rule' => $rule,
                'children' => collect()
            ];
        });

        $nodes->each(function ($node) use ($nodes) {
            if ($node->rule->parent_id) {
                $nodes->get($node->rule->parent_id)->children->push($node);
            }
        });

        return $nodes->filter(fn($node) => !$node->rule->parent_id)->values();
    }

    protected function transformData(Collection $tree): array
    {
        return $tree->mapWithKeys(function ($node) {
            return [$node->rule->external_field => $this->processNode($node)];
        })->all();
    }

    protected function processNode(object $node)
    {
        return match ($node->rule->data_type) {
            'attribute' => $this->processAttribute($node->rule),
            'object' => $this->processObject($node),
            'array' => $this->processArray($node),
            'object_list' => $this->processObjectList($node),
            default => throw new TransformationException(
                "Unknown data type: {$node->rule->data_type}",
                $node->rule->external_field
            )
        };
    }

    protected function processAttribute(MappingRule $rule)
    {
        $value = $this->getValueOfInternalField($rule->internal_field);
        return $this->validateAndTransform($rule, $value);
    }

    protected function processObject(object $node): array
    {
        return $node->children->mapWithKeys(function ($child) {
            return [$child->rule->external_field => $this->processNode($child)];
        })->all();
    }

    protected function processArray(object $node): array
    {
        return $node->children
            ->map(fn($child) => $this->processNode($child))
            ->filter()
            ->values()
            ->all();
    }

    protected function processObjectList(object $node): array
    {
        $collection = $this->getCollectionForObjectList($node->rule->internal_field);

        return $collection->map(function ($item) use ($node) {
            return $node->children->mapWithKeys(function ($child) use ($item) {
                $value = data_get($item, $child->rule->internal_field);
                return [
                    $child->rule->external_field =>
                        $this->validateAndTransform($child->rule, $value)
                ];
            })->all();
        })->all();
    }

    protected function getCollectionForObjectList(string $field): Collection
    {
        return match ($field) {
            'claim_statuses' => $this->claim->claimStatus,
            default => throw new TransformationException("Unknown collection field: {$field}")
        };
    }

    protected function validateAndTransform(MappingRule $rule, $value)
    {
        if ($rule->is_required && is_null($value)) {
            if (!is_null($rule->default_value)) {
                return $rule->default_value;
            }
            throw new TransformationException(
                "Required field {$rule->external_field} is missing",
                $rule->external_field,
                $value
            );
        }

        return $value ?? $rule->default_value ?? null;
    }

    protected function getValueOfInternalField(?string $fieldName)
    {
        if (!$fieldName) {
            return null;
        }

        if (!str_contains($fieldName, '.')) {
            return $this->claim->{$fieldName};
        }

        [$relation, $field] = explode('.', $fieldName);
        return $this->claim->{$relation}->{$field} ?? null;
    }
}