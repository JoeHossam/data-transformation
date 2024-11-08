<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'payer_id', 'authorization_notes', 'internal_notes'];
    public $timestamps = false;

    public function payer(): BelongsTo
    {
        return $this->belongsTo(Payer::class);
    }

    public function claimStatus(): HasMany
    {
        return $this->hasMany(ClaimStatus::class);
    }
}