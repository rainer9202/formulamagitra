<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $modelLabel = 'usuario';
    protected static ?string $pluralModelLabel = 'usuarios';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 3;

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'super_admin');
            }
        )->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'super_admin');
            }
        );
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
                    ->email()
                    ->label('Correo electronico')
                    ->required()
                    ->unique(table: 'users', ignoreRecord: true)
                    ->maxLength(255),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->label('Role'),
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
                TextColumn::make('name')->searchable()->sortable()->label('Nombre'),
                TextColumn::make('email')->searchable()->sortable()->label('Correo'),
                TextColumn::make('roles.name')->label('Roles'),
                IconColumn::make('active')->label('Activo')->sortable()->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
