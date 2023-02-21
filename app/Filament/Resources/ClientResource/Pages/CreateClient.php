<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

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
        $user->assignRole('cliente');

        return static::getModel()::create([
            'user_id' => $user->id,
            'nif' => $data['nif'],
            'description' => $data['description'],
            'city' => $data['city'],
            'province' => $data['province'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'phone' => $data['phone'],
        ]);
    }
}
