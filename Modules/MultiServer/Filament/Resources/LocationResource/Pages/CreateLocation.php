<?php

namespace Modules\MultiServer\Filament\Resources\LocationResource\Pages;

use Modules\MultiServer\Filament\Resources\LocationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
