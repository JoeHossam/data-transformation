<?php

namespace App\Services\DataTransformation\Interfaces;

use App\Models\MappingRule;
use App\Services\DataTransformation\Helpers\TransformationContext;

interface DataBuilder
{
    public function build(MappingRule $node, TransformationContext $context);
}