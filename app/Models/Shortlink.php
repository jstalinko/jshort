<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'short',
        'real_url',
        'cloak_url',
        'total_allowed',
        'total_blocked',
        'block_vpn',
        'block_crawler',
        'logs',
        'block_isp',
        'block_ip',
        'lock_country',
        'lock_browser',
        'lock_device',
        'lock_os',
        'lock_referer',
        'throttle',
        'method',
        'active',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
