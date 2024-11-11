<?php

namespace App\Services\DataTransformation\Builders;

use App\Models\MappingRule;
use App\Services\DataTransformation\Helpers\BuilderFactory;
use App\Services\DataTransformation\Helpers\TransformationContext;
use App\Services\DataTransformation\Interfaces\DataBuilder;
use App\Services\DataTransformation\Validators\ValidationService;

class ArrayBuilder implements DataBuilder
{
    public function __construct(
        private readonly ValidationService $validator
    ) {
    }

    public function build(MappingRule $node, TransformationContext $context)
    {
        $children = $node->children()->get();

        return $children->map(function ($child) use ($context) {
            $builder = (new BuilderFactory($this->validator))->createBuilder($child->data_type);
            return $builder->build($child, $context);
        })->all();
    }
}