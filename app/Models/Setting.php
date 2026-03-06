<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get all values for a specific setting key
     */
    public static function getOptions(string $key): array
    {
        return self::where('key', $key)
            ->pluck('value', 'id')
            ->toArray();
    }

    /**
     * Add a new option
     */
    public static function addOption(string $key, string $value): bool
    {
        // Check if already exists
        if (self::where('key', $key)->where('value', $value)->exists()) {
            return false;
        }
        
        self::create(['key' => $key, 'value' => $value]);
        return true;
    }

    /**
     * Delete an option
     */
    public static function deleteOption(int $id): bool
    {
        return self::where('id', $id)->delete();
    }
}
