<?php

namespace App\Services\DataTransformation\Builders;

use App\Exceptions\TransformationException;
use App\Models\Claim;
use App\Models\MappingRule;
use App\Services\DataTransformation\Validators\ValidationService;
use Illuminate\Support\Collection;
use App\Services\DataTransformation\Helpers\TransformationContext;
use App\Services\DataTransformation\Interfaces\DataBuilder;

class ObjectListBuilder implements DataBuilder
{
    public function __construct(
        private readonly ValidationService $validator
    ) {
    }

    public function build(MappingRule $node, TransformationContext $context)
    {
        $collection = $this->getCollectionForObjectList(
            $node->internal_field,
            claim: $context->getClaim()
        );

        $colsToShow = $node->children()->get();

        return $collection->map(function ($row) use ($node) {
            $data = $row->toArray();
            return $node->children()->get()->mapWithKeys(function ($child) use ($data) {
                $value = data_get($data, $child->internal_field);
                return [
                    $child->external_field =>
                        $this->validator->validateAndTransform($child, $value)
                ];
            })->all();
        })->all();
    }

    private function getCollectionForObjectList(string $field, Claim $claim): Collection
    {
        return match ($field) {
            'claim_statuses' => $claim->claimStatus,
            default => throw new TransformationException("Unknown collection field: {$field}")
        };
    }
}