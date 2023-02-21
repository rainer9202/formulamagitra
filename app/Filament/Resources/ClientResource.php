<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $navigationGroup = 'Negocio';
    protected static ?int $navigationSort = 1;

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->hidden(fn (string $context): bool => $context === 'edit')
                    ->email()
                    ->label('Correo electronico')
                    ->required()
                    ->unique(table: 'users', ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('nif')
                    ->label('NIF')
                    ->unique(table: 'clients', ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->columnSpan(2),
                TextInput::make('address')
                    ->label('Direccion')
                    ->maxLength(255),
                TextInput::make('city')
                    ->label('Cuidad')
                    ->maxLength(255),
                TextInput::make('province')
                    ->label('Provincia')
                    ->maxLength(255),
                TextInput::make('postal_code')
                    ->label('Codigo Postal')
                    ->maxLength(255),
                Fieldset::make('Seguridad')
                    ->schema([
                        Toggle::make('active')
                            ->label('Activo')
                            ->inline()
                            ->columnSpan(2),
                        TextInput::make('password')
                            ->confirmed()
                            ->minLength(6)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(6)
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Confirmar Contraseña'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable()->sortable()->label('Nombre'),
                TextColumn::make('user.email')->searchable()->sortable()->label('Correo'),
                TextColumn::make('nif')->searchable()->sortable()->label('NIF'),
                TextColumn::make('city')->searchable()->sortable()->label('Cuidad'),
                IconColumn::make('user.active')->label('Activo')->sortable()->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Fecha creacion'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
