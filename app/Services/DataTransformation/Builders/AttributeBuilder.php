<?php

namespace App\Services\DataTransformation\Builders;

use App\Models\MappingRule;
use App\Services\DataTransformation\Helpers\TransformationContext;
use App\Services\DataTransformation\Interfaces\DataBuilder;
use App\Services\DataTransformation\Validators\ValidationService;

class AttributeBuilder implements DataBuilder
{
    public function __construct(
        private readonly ValidationService $validator
    ) {
    }

    public function build(MappingRule $node, TransformationContext $context)
    {
        $value = $context->getValueFromClaim($node->internal_field);
        return $this->validator->validateAndTransform($node, $value);
    }
}