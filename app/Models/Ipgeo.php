<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ipgeo extends Model
{
    use HasFactory;

    protected $fillable = [
            'ip',
            'country_name',
            'country_code',
            'region_code',
            'region_name',
            'city',
            'zip',
            'isp',
            'lon',
            'lat',
            'is_proxy',
            'is_hosting',
            'by',
    ];

    public static function ipExist($ip)
    {
        $ipAddress = self::where('ip', $ip)->first();

        if ($ipAddress) {
            foreach ($ipAddress->getFillable() as $column) {
                if (is_null($ipAddress->$column)) {
                    return false; 
                }
            }
            return true; // All columns are filled
        }

        return false; // IP address does not exist
    }
}
