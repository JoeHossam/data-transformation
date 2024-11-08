<?php

namespace App\Exceptions;

use Exception;

class TransformationException extends Exception
{
    protected $field;
    protected $value;

    public function __construct(string $message, string $field = null, $value = null)
    {
        parent::__construct($message);
        $this->field = $field;
        $this->value = $value;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }
}
