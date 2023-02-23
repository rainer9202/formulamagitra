<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $modelLabel = 'factura';
    protected static ?string $pluralModelLabel = 'Facturas';
    protected static ?string $navigationGroup = 'Negocio';
    protected static ?int $navigationSort = 4;

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number')
                    ->label('Identificador'),
                TextInput::make('created_at')
                    ->label('Fecha creacion'),
                FileUpload::make('file')
                    ->label('Factura')
                    ->disk('public')
                    ->directory('order-detail')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                    })
                    ->columnSpan(2)
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable()->label('Identificador'),
                TextColumn::make('order.id')->searchable()->label('Orden ID'),
                TextColumn::make('order.provider.user.name')->searchable()->label('Proveedor'),
                TextColumn::make('order.provider.nif')->searchable()->label('Prov. NIF'),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Fecha creacion'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('exportFactura')
                    ->label('Factura')
                    ->action(function ($record) {
                        $name = 'invoice_' . $record->id . '_' . Date::now();
                        return response()->streamDownload(function () use ($record) {
                            $return = $record->file;
                            echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                        }, $name . '.pdf');
                    })
                    ->icon('heroicon-s-download')
                    ->color('primary'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvoices::route('/'),
        ];
    }
}
