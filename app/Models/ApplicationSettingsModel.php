<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationSettingsModel extends Model
{
    protected $table = 'application_settings';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_key' => 'string',
            'secret_key' => 'string',
            'allowed_ips' => 'array',
        ];
    }
}
