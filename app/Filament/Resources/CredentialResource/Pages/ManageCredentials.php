<?php

namespace App\Filament\Resources\CredentialResource\Pages;

use App\Filament\Resources\CredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;

class ManageCredentials extends ManageRecords
{
    protected static string $resource = CredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = Filament::auth()->user()->id;
                    return $data;
                }),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.pages.copy-credentials-script');
    }
}

