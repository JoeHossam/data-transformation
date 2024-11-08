<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaimStatus extends Model
{
    use HasFactory;

    protected $fillable = ['claim_id', 'status', 'date'];
    public $timestamps = false;

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}