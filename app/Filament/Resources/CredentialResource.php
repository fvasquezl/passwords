<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CredentialResource\Pages;
use App\Filament\Resources\CredentialResource\RelationManagers;
use App\Models\Credential;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class CredentialResource extends Resource
{
    protected static ?string $model = Credential::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Credentials';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Fieldset::make('Access Info')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn($context) => $context === 'create')
                            ->revealable()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Forms\Components\Select::make('category_id')->relationship('category', 'name')->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist

            ->schema([
                Infolists\Components\ViewEntry::make('credentials')
                    ->view('filament.infolists.credentials-with-copy')
                    ->state(function (Credential $record) {
                        return [
                            'title' => $record->title,
                            'username' => $record->username,
                            'password' => $record->password,
                            'description' => $record->description ?? 'No description',
                            'category' => $record->category->name ?? 'No category',
                        ];
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->state(fn($record) => strip_tags($record->description))
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\BadgeColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shared_info')
                    ->label('Shared')
                    ->state(function (Credential $record) {
                        $currentUserId = Filament::auth()->user()->id;
                        if ($record->user_id == $currentUserId) {
                            return 'Owner';
                        }

                        $share = $record->shares()->where('shared_with_user_id', $currentUserId)->first();
                        if ($share) {
                            return 'Shared by ' . $share->sharedBy->name;
                        }

                        return 'Owner';
                    })
                    ->badge()
                    ->color(function (Credential $record) {
                        $currentUserId = Filament::auth()->user()->id;
                        return $record->user_id == $currentUserId ? 'success' : 'warning';
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('View Credentials'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('share')
                    ->label('Share')
                    ->icon('heroicon-o-share')
                    ->visible(fn(Credential $record) => $record->canBeShared())
                    ->form([
                        Forms\Components\Select::make('shared_with_user_id')
                            ->label('Share with User')
                            ->options(function () {
                                return \App\Models\User::where('id', '!=', Filament::auth()->user()->id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('permission')
                            ->label('Permission')
                            ->options([
                                'read' => 'Read Only',
                                'write' => 'Read & Write',
                            ])
                            ->default('read')
                            ->required(),
                    ])
                    ->action(function (array $data, Credential $record) {
                        \App\Models\CredentialShare::updateOrCreate(
                            [
                                'credential_id' => $record->id,
                                'shared_with_user_id' => $data['shared_with_user_id'],
                            ],
                            [
                                'shared_by_user_id' => Filament::auth()->user()->id,
                                'permission' => $data['permission'],
                            ]
                        );
                    })
                    ->modalHeading('Share Credential'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Filament::auth()->user()->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCredentials::route('/'),
        ];
    }
}

