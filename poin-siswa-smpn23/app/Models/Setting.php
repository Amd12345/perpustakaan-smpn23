<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'description'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'umum'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group],
        );
    }
}
