<?php

namespace App\Services\DataTransformation\Builders;

use App\Models\MappingRule;
use App\Services\DataTransformation\Helpers\BuilderFactory;
use App\Services\DataTransformation\Helpers\TransformationContext;

class ObjectBuilder extends AbstractDataBuilder
{
    public function build(MappingRule $node, TransformationContext $context)
    {
        return $node->children()->get()->mapWithKeys(function ($child) use ($context) {
            $builder = (new BuilderFactory($this->validator))->createBuilder($child->data_type);
            return [$child->external_field => $builder->build($child, $context)];
        })->all();
    }
}