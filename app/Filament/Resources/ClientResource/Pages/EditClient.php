<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::find($data['user_id']);

        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['active'] = $user->active;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = $record->user;
        $user->name = $data['name'];
        $user->active = $data['active'];

        if (isset($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();
        $record->update($data);

        return $record;
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
