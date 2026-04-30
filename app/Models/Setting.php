<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description', 'group', 'is_encrypted'];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Helper to get a setting value easily (with auto-decryption)
     */
    public static function get(string $key, $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        $value = $setting->value;

        if ($setting->is_encrypted && $value) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception) {
                // Return raw if decrypt fails (e.g., old data before encryption)
            }
        }

        return match ($setting->type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default   => $value,
        };
    }

    /**
     * Upsert a setting safely
     */
    public static function set(string $key, mixed $value, string $group = 'general', bool $encrypt = false): void
    {
        if ($encrypt && $value) {
            $value = Crypt::encryptString((string) $value);
        }

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'is_encrypted' => $encrypt]
        );
    }

    /**
     * Get all settings in a group as key=>value array (decrypted)
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)->get()
            ->mapWithKeys(function ($setting) {
                $value = $setting->value;
                if ($setting->is_encrypted && $value) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Exception) {}
                }
                return [$setting->key => $value];
            })->toArray();
    }
}
