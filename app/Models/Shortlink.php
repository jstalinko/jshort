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

    public function getLockCountryAttribute($value)
    {
        if(strpos($value ,",") !== false){
            return explode(',', $value);
        }else{
            return [$value];
        }
    }
    public function getLockDeviceAttribute($value)
    {
        if(strpos($value ,",") !== false){
            return explode(',', $value);
        }else{
            return [$value];
        }
    }

    public function getLockOsAttribute($value)
    {
        if(strpos($value ,",") !== false){
            return explode(',', $value);
        }else{
            return [$value];
        }
    }
    public function getLockRefererAttribute($value)
    {
        if(strpos($value ,",") !== false){
            return explode(',', $value);
        }else{
            return [$value];
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
