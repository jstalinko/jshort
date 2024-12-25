<?php

namespace App\Filament\Resources\ShortlinkResource\Pages;

use App\Services\Jd;
use App\Models\Shortlink;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ShortlinkResource;
use Filament\Tables\Columns\Concerns\HasRecord;

class StatShortlink extends Page
{
    use HasRecord;
    protected static string $resource = ShortlinkResource::class;
    
    protected static  ?string  $model = Shortlink::class;
    protected static ?string $title = 'Statistic';
    protected static string $view = 'filament.resources.shortlink-resource.pages.stat-shortlink';

    public function mount($record)
    {
        $this->record = Shortlink::findOrFail($record);
    }
    public function getViewData(): array
{
    
    $logpath = Jd::log_path($this->record->user_id , $this->record->id);
    //dd($logpath);
    if(file_exists($logpath . '/allowed.log'))
    {
        $log_allowed = file_get_contents($logpath.'/allowed.log');
    }else{
        $log_allowed = 'No Data';
    }

    if(file_exists($logpath.'/blocked.log'))
    {
        $log_blocked = file_get_contents($logpath.'/blocked.log');
    }else{
        $log_blocked = 'No Data';
    }
    return [
        'totalBlocked' => $this->record->total_allowed, // Replace with your actual data
        'totalAllowed' => $this->record->total_blocked, // Replace with your actual data
        'logs_allowed' => $log_allowed, 
        'logs_blocked' => $log_blocked,
        'short' => $this->record
    ];
}
}
