<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\TemporaryUploadedFile;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'elemento';
    protected static ?string $pluralModelLabel = 'Elementos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Textarea::make('observations')
                    ->columnSpan(2)
                    ->label('Observaciones'),
                FileUpload::make('image_1')
                    ->label('Imagen 1')
                    ->disk('public')
                    ->directory('order-detail')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                    })
                    ->visibility('public'),
                FileUpload::make('image_2')
                    ->label('Imagen 2')
                    ->disk('public')
                    ->directory('order-detail')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                    })
                    ->visibility('public'),
                FileUpload::make('image_3')
                    ->label('Imagen 3')
                    ->disk('public')
                    ->directory('order-detail')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                    })
                    ->visibility('public'),
                FileUpload::make('image_4')
                    ->label('Imagen 4')
                    ->disk('public')
                    ->directory('order-detail')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                    })
                    ->visibility('public')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Nombre'),
                TextColumn::make('quantity')->searchable()->sortable()->label('Cantidad'),
                TextColumn::make('observations')->limit(80)->wrap()->label('Observaciones'),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Fecha creacion'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
