<?php

namespace App\Filament\Resources\ShortlinkResource\Pages;

use App\Filament\Resources\ShortlinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShortlink extends ViewRecord
{
    protected static string $resource = ShortlinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
