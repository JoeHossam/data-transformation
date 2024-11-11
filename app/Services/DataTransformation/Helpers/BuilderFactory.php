<?php

namespace App\Services\DataTransformation\Helpers;

use App\Exceptions\TransformationException;
use App\Services\DataTransformation\Builders\ArrayBuilder;
use App\Services\DataTransformation\Builders\AttributeBuilder;
use App\Services\DataTransformation\Builders\ObjectBuilder;
use App\Services\DataTransformation\Builders\ObjectListBuilder;
use App\Services\DataTransformation\Interfaces\DataBuilder;
use App\Services\DataTransformation\Validators\ValidationService;

class BuilderFactory
{
    private array $builders = [
        'attribute' => AttributeBuilder::class,
        'object' => ObjectBuilder::class,
        'array' => ArrayBuilder::class,
        'object_list' => ObjectListBuilder::class,
    ];

    public function __construct(
        private readonly ValidationService $validator
    ) {
    }

    public function createBuilder(string $type): DataBuilder
    {
        if (!isset($this->builders[$type])) {
            throw new TransformationException("Unknown data type: {$type}");
        }

        return new $this->builders[$type]($this->validator);
    }
}