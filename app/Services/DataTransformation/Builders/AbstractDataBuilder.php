<?php

namespace App\Services\DataTransformation\Builders;

use App\Models\MappingRule;
use App\Services\DataTransformation\Helpers\TransformationContext;
use App\Services\DataTransformation\Interfaces\DataBuilder;
use App\Services\DataTransformation\Validators\ValidationService;

abstract class AbstractDataBuilder implements DataBuilder
{
    public function __construct(
        protected readonly ValidationService $validator
    ) {}

    abstract public function build(MappingRule $node, TransformationContext $context);
}
