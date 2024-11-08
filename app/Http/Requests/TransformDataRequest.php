<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransformDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'claim_id' => ['required', 'integer', 'exists:claims,id'],
            'endpoint_id' => ['required', 'integer', 'exists:mapping_rules,endpoint_id'],
        ];
    }
}