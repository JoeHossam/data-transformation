<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone'];
    public $timestamps = false;

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
}