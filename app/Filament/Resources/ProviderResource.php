<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
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
use Livewire\TemporaryUploadedFile;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $modelLabel = 'proveedor';
    protected static ?string $pluralModelLabel = 'Proveedores';
    protected static ?string $navigationGroup = 'Negocio';
    protected static ?int $navigationSort = 2;

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
                    ->unique(table: 'providers', ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('logo')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('providers')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                    })
                    ->visibility('public')
                    ->columnSpan(2),
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
                Tables\Columns\ImageColumn::make('logo')->label('Imagen'),
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
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
