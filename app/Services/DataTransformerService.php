<?php

namespace App\Services;

use App\Models\Claim;
use App\Services\DataTransformation\Helpers\BuilderFactory;
use App\Services\DataTransformation\Helpers\CacheManager;
use App\Services\DataTransformation\Helpers\TransformationContext;
use App\Services\DataTransformation\Repositories\SchemaRepository;
use App\Services\DataTransformation\Validators\ValidationService;
use Log;

class DataTransformerService
{
    public function __construct(
        private readonly BuilderFactory $builderFactory,
        private readonly SchemaRepository $schemaRepository,
        private readonly CacheManager $cacheManager,
        private readonly ValidationService $validator
    ) {
    }

    public function transform(int $claimId, int $endpointId): array
    {
        return $this->cacheManager->remember(
            $claimId,
            $endpointId,
            fn() => $this->startTransformation($claimId, $endpointId)
        );
    }

    private function startTransformation(int $claimId, int $endpointId): array
    {
        try {
            $schema = $this->schemaRepository->getSchemaRoots(endpointId: $endpointId);
            $claim = Claim::findOrFail($claimId);

            $context = new TransformationContext($claim, $schema);

            return $this->transformData($context);
        } catch (\Exception $e) {
            Log::error('Data transformation failed', [
                'claim_id' => $claimId,
                'endpoint_id' => $endpointId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function transformData(TransformationContext $context): array
    {
        return $context->getSchema()->mapWithKeys(function ($node) use ($context) {
            $builder = $this->builderFactory->createBuilder($node->data_type);
            return [
                $node->external_field => $builder->build($node, $context)
            ];
        })->all();
    }
}
