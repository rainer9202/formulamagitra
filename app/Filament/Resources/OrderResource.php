<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\DetailsRelationManager;
use App\Models\Order;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Livewire\TemporaryUploadedFile;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'orden';
    protected static ?string $pluralModelLabel = 'Ordenes';
    protected static ?string $navigationGroup = 'Negocio';
    protected static ?int $navigationSort = 3;

    protected static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if ($user->hasRole('proveedor')) {
            $providerId = $user->provider->id;
            return parent::getEloquentQuery()->where('provider_id', $providerId)->count();
        }
        if ($user->hasRole('cliente')) {
            return parent::getEloquentQuery()->where('user_id', $user->id)->count();
        }
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->hasRole('proveedor')) {
            $providerId = $user->provider->id;
            return parent::getEloquentQuery()->where('provider_id', $providerId);
        }
        if ($user->hasRole('cliente')) {
            return parent::getEloquentQuery()->where('user_id', $user->id);
        }
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('provider_id')
                    ->required()
                    ->label('Proveedor')
                    ->options(Provider::with('user')->get()->pluck('user.name', 'id'))
                    ->searchable(),
                Select::make('status')
                    ->reactive()
                    ->hidden(fn (string $context): bool => $context === 'create')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'Recibido' => 'Recibido',
                        'En preparación' => 'En preparación',
                        'Recoger' => 'Recoger',
                        'Entregado' => 'Entregado'

                    ]),
                Fieldset::make('Datos de la factura')
                    ->hidden(function (callable $get) {
                        return $get('status') === 'Entregado' ? false : true;
                    })
                    ->schema([
                        Textarea::make('notes')
                            ->columnSpan(2)
                            ->label('Nota'),
                        FileUpload::make('file')
                            ->label('Factura')
                            ->disk('public')
                            ->directory('invoice')
                            ->preserveFilenames()
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                return (string)str($file->getClientOriginalName())->remove([' '])->prepend(uniqid() . '-');
                            })
                            ->columnSpan(2)
                            ->visibility('public'),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.user.name')->searchable()->sortable()->label('Proveedor'),
                TextColumn::make('status')->searchable()->sortable()->label('Estado'),
                TextColumn::make('status_date')->date()->sortable()->label('Fecha estado'),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Fecha creacion'),
            ])
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
            DetailsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
