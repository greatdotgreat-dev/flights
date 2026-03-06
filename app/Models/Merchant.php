<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = [
        'name',
        'code',
        'account_number',
        'currency',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bookingCards(): HasMany
    {
        return $this->hasMany(BookingCard::class);
    }
    public function merchant()
    {
    return $this->belongsTo(Merchant::class);
    }

}
