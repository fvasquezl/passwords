<?php

namespace App\Filament\Resources\SharedCredentialResource\Pages;

use App\Filament\Resources\SharedCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\View\View;

class ManageSharedCredentials extends ManageRecords
{
    protected static string $resource = SharedCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.pages.copy-credentials-script');
    }
}
