<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\TemporaryUploadedFile;

class UserProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Mi Perfil';
    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static string $view = 'filament.user-profile';
    protected static bool $shouldRegisterNavigation = false;

    public function register()
    {
        $form = $this->form->getState();
        $user = Auth::user();

        $user->name = $form['name'];
        if ($form['email'] != $user->email) {
            $user->email = $form['email'];
        }

        if ($user->hasRole('proveedor')) {

            $user->provider->nif = $form['nif'];
            $user->provider->description = $form['description'];
            $user->provider->logo = $form['logo'];
            $user->provider->city = $form['city'];
            $user->provider->province = $form['province'];
            $user->provider->address = $form['address'];
            $user->provider->postal_code = $form['postal_code'];
            $user->provider->phone = $form['phone'];

            if ($user->provider->nif != $form['nif']) {
                $user->provider->nif = $form['nif'];
            }

            $user->provider->save();
        }

        if ($user->hasRole('cliente')) {

            $user->client->nif = $form['nif'];
            $user->client->description = $form['description'];
            $user->client->city = $form['city'];
            $user->client->province = $form['province'];
            $user->client->address = $form['address'];
            $user->client->postal_code = $form['postal_code'];
            $user->client->phone = $form['phone'];

            if ($user->client->nif != $form['nif']) {
                $user->client->nif = $form['nif'];
            }

            $user->client->save();
        }

        if (isset($form['password'])) {
            $user->password = $form['password'];
        }

        $user->save();

        Notification::make()
            ->title('Datos guardados correctamente')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
        if ($user->hasRole('proveedor')) {
            $this->form->fill([
                'nif' => $user->provider->nif,
                'description'  => $user->provider->description,
                'logo' => $user->provider->logo,
                'city' => $user->provider->city,
                'province' => $user->provider->province,
                'address' => $user->provider->address,
                'postal_code' => $user->provider->postal_code,
                'phone' => $user->provider->phone,
            ]);
        }
        if ($user->hasRole('cliente')) {
            $this->form->fill([
                'nif' => $user->client->nif,
                'description'  => $user->client->description,
                'city' => $user->client->city,
                'province' => $user->client->province,
                'address' => $user->client->address,
                'postal_code' => $user->client->postal_code,
                'phone' => $user->client->phone,
            ]);
        }
    }

    protected function getFormSchema(): array
    {
        $user = Auth::user();
        $defaultInputs = [
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->unique(ignorable: fn ($record) => $record)
                ->maxLength(255),
            Fieldset::make('Seguridad')
                ->schema([
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
        ];
        if ($user->hasRole('proveedor')) {
            $defaultInputs = $this->setInputFromUserRole('proveedor');
        }
        if ($user->hasRole('cliente')) {
            $defaultInputs = $this->setInputFromUserRole('cliente');
        }

        return $defaultInputs;
    }

    public function setInputFromUserRole(string $role)
    {

        if ($role === 'proveedor') {
            return [
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->label('Correo electronico')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                TextInput::make('nif')
                    ->label('NIF')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->columnSpan(2)
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
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('city')
                    ->label('Cuidad')
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('province')
                    ->label('Provincia')
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('postal_code')
                    ->label('Codigo Postal')
                    ->maxLength(255)
                    ->columnSpan(2),
                Fieldset::make('Seguridad')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('password')
                            ->confirmed()
                            ->minLength(6)
                            ->label('Contraseña')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->minLength(6)
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Confirmar Contraseña'),
                    ])
                    ->columns(2),
            ];
        }
        if ($role === 'cliente') {
            return [
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->label('Correo electronico')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                TextInput::make('nif')
                    ->label('NIF')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->columnSpan(2)
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->columnSpan(2),
                TextInput::make('address')
                    ->label('Direccion')
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('city')
                    ->label('Cuidad')
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('province')
                    ->label('Provincia')
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('postal_code')
                    ->label('Codigo Postal')
                    ->maxLength(255)
                    ->columnSpan(2),
                Fieldset::make('Seguridad')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('password')
                            ->confirmed()
                            ->minLength(6)
                            ->label('Contraseña')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->minLength(6)
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Confirmar Contraseña'),
                    ])
                    ->columns(2),
            ];
        }
    }
}
