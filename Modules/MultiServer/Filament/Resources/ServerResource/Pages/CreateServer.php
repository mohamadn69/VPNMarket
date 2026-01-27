<?php

namespace Modules\MultiServer\Filament\Resources\ServerResource\Pages;

use Modules\MultiServer\Filament\Resources\ServerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServer extends CreateRecord
{
    protected static string $resource = ServerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
