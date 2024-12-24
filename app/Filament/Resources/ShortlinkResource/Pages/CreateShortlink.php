<?php

namespace App\Filament\Resources\ShortlinkResource\Pages;

use App\Filament\Resources\ShortlinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShortlink extends CreateRecord
{
    protected static string $resource = ShortlinkResource::class;

    protected  function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        $data['lock_country'] = is_array($data['lock_country']) ? implode(",", $data['lock_country']) : $data['lock_country'];
        $data['lock_device'] = is_array($data['lock_device']) ? implode(",", $data['lock_device']) : $data['lock_device'];
        $data['lock_os'] = is_array($data['lock_os']) ? implode(",", $data['lock_os']) : $data['lock_os'];
        $data['lock_referer'] = is_array($data['lock_referer']) ? implode(",", $data['lock_referer']) : $data['lock_referer'];




        return $data;
    }
}
