<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Invoice;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['file']) && $data['status'] === 'Entregado') {
            Invoice::create([
                'user_id' => $record->user_id,
                'provider_id' => Auth::id(),
                'order_id' => $record->id,
                'number' => Str::uuid()->toString(),
                'file' => $data['file'],
                'notes' => $data['notes'],
            ]);
        }
        if ($record->status !== $data['status'])
            $data['status_date'] = Date::now();

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
