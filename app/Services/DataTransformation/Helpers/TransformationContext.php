<?php

namespace App\Services\DataTransformation\Helpers;

use App\Models\Claim;
use Illuminate\Support\Collection;

class TransformationContext
{
    public function __construct(
        private readonly Claim $claim,
        private readonly Collection $schema
    ) {
    }

    public function getClaim(): Claim
    {
        return $this->claim;
    }

    public function getSchema(): Collection
    {
        return $this->schema;
    }

    public function getValueFromClaim(?string $fieldName)
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
