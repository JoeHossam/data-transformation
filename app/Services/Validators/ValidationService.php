<?php

namespace App\Services\DataTransformation\Validators;

use App\Exceptions\TransformationException;
use App\Models\MappingRule;

class ValidationService
{
    public function validateAndTransform(MappingRule $rule, $value)
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
}