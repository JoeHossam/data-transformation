<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MappingRule extends Model
{
    use HasFactory;
    protected $fillable = [
        'internal_field',
        'external_field',
        'data_type',
        'parent_id',
        'endpoint_id',
        'is_required',
        'default_value'
    ];
}