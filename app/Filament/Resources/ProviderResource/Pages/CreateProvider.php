<?php

namespace App\Filament\Resources\ProviderResource\Pages;

use App\Filament\Resources\ProviderResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProvider extends CreateRecord
{
    protected static string $resource = ProviderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'active' => $data['active'],
            'password' => $data['password'],
        ]);
        $user->assignRole('proveedor');

        return static::getModel()::create([
            'user_id' => $user->id,
            'nif' => $data['nif'],
            'description' => $data['description'],
            'logo' => $data['logo'],
            'city' => $data['city'],
            'province' => $data['province'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'phone' => $data['phone'],
        ]);
    }
}
