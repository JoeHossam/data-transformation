<?php

namespace App\Services\DataTransformation\Builders;

use App\Models\MappingRule;
use App\Services\DataTransformation\Helpers\TransformationContext;

class AttributeBuilder extends AbstractDataBuilder
{
    public function build(MappingRule $node, TransformationContext $context)
    {
        $value = $context->getValueFromClaim($node->internal_field);
        return $this->validator->validateAndTransform($node, $value);
    }
}