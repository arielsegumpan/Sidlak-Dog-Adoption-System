<?php

namespace App\Filament\Resources\Animal;

use App\Enums\AdoptionEnum;
use App\Filament\Resources\Animal\AdoptionResource\Pages;
use App\Filament\Resources\Animal\AdoptionResource\RelationManagers;
use App\Models\Adoption\Adoption;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
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

class AdoptionResource extends Resource
{
    protected static ?string $model = Adoption::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    protected static ?string $navigationGroup = 'Animal';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Adoption Information')
                ->description('Adoption information for the animal.')
                ->schema([
                    TextInput::make('adoption_number')
                    ->default('AR-'. date('Ymd-') . random_int(100000, 999999))
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(32)
                    ->unique(Adoption::class, 'adoption_number', ignoreRecord: true)
                    ->columnSpanFull(),

                    Select::make('user_id')
                    ->relationship('user', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->optionsLimit(6),

                    Select::make('dog_id')
                    ->relationship(
                        name: 'dog',
                        titleAttribute: 'dog_name',
                        modifyQueryUsing: function (Builder $query, string $operation){
                            if($operation == 'create'){
                                $query->whereDoesntHave('adoption', fn (Builder $query) => $query->where('status', 'approved'));
                            }
                        },
                    )
                    ->getOptionLabelsUsing(fn (Model $record) => "{$record->name}")
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->optionsLimit(6),

                    DatePicker::make('request_date')
                    ->required()
                    ->native(false)
                    ->default(now())
                    ->dehydrated(),

                    ToggleButtons::make('status')
                    ->required()
                    ->options(AdoptionEnum::class)
                    ->default('pending')
                    ->colors([
                        'pending' => 'primary',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    ])->inline(true),

                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                    'default' => 2
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adoption_number')
                ->label('Adoption #')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('success'),

                TextColumn::make('user.name')
                ->label('Adopter')
                ->searchable()
                ->sortable(),

                TextColumn::make('dog.dog_name')->searchable()->label('Dog Name')->sortable(),

                TextColumn::make('status')->label('Status')->toggleable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'primary',
                    'approved' => 'success',
                    'rejected' => 'danger',
                }),


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
                ->label(__('New Adoption')),
            ])
            ->emptyStateIcon('heroicon-o-face-smile')
            ->emptyStateHeading('No adoptions are created');
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
            'index' => Pages\ListAdoptions::route('/'),
            'create' => Pages\CreateAdoption::route('/create'),
            'edit' => Pages\EditAdoption::route('/{record}/edit'),
        ];
    }
}
