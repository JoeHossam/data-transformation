<?php

namespace App\Services\DataTransformation\Repositories;

use App\Exceptions\TransformationException;
use App\Models\MappingRule;
use Illuminate\Support\Collection;

class SchemaRepository
{
    public function getSchemaRoots(int $endpointId): Collection
    {
        $schema = MappingRule::where('endpoint_id', $endpointId)::where('parent_id', null)->get();

        if ($schema->isEmpty()) {
            throw new TransformationException("No schema found for endpoint {$endpointId}");
        }

        return $schema;
    }
}