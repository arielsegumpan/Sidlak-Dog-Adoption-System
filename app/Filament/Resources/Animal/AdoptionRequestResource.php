<?php

namespace App\Filament\Resources\Animal;

use App\Enums\AdoptionRequestEnum;
use App\Filament\Resources\Animal\AdoptionRequestResource\Pages;
use App\Filament\Resources\Animal\AdoptionRequestResource\RelationManagers;
use App\Models\Adoption\AdoptionRequest as AdoptionAdoptionRequest;
use App\Models\Animal\AdoptionRequest;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdoptionRequestResource extends Resource
{
    protected static ?string $model = AdoptionAdoptionRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Adoption Request Details')
                ->schema([

                    TextInput::make('adoption_number')
                    ->default('AR-'. date('Y-md') . random_int(100000, 999999))
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(32)
                    ->unique(AdoptionAdoptionRequest::class, 'adoption_number', ignoreRecord: true),

                    Select::make('dog_id')
                    ->required()
                    ->searchable()
                    ->optionsLimit(6)
                    ->preload()
                    ->relationship(
                        name: 'dog',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, string $operation){
                            if($operation == 'create'){
                                $query->whereDoesntHave('adoption_request', fn (Builder $query) => $query->where('status', 'adopted'));
                            }
                        },
                    )
                    ->getOptionLabelsUsing(fn (Model $record) => "{$record->name}"),




                    Select::make('user_id')
                    ->required()
                    ->searchable()
                    ->optionsLimit(6)
                    ->preload()
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                    ),

                    ToggleButtons::make('status')
                    ->options(AdoptionRequestEnum::class)
                    ->default('pending')
                    ->colors([
                        'adopted' => 'primary',
                        'available' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                    ]),


                ])
,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adoption_number')->searchable()->label('Adoption Number'),
                TextColumn::make('dog.name')->searchable()->label('Dog Name'),
                TextColumn::make('user.name')->searchable()->label('User Name'),
                TextColumn::make('status')->label('Status')->toggleable(),
                TextColumn::make('created_at')->label('Created At')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading()
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('Create Request')),
            ])
            ->emptyStateIcon('heroicon-o-information-circle')
            ->emptyStateHeading('No adoption requests are created');
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
            'index' => Pages\ListAdoptionRequests::route('/'),
            'create' => Pages\CreateAdoptionRequest::route('/create'),
            'edit' => Pages\EditAdoptionRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
