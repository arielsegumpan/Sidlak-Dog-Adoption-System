<?php

namespace App\Filament\Resources\Animal;

use App\Enums\AdoptionEnum;
use App\Filament\Resources\Animal\AdoptionResource\Pages;
use App\Filament\Resources\Animal\AdoptionResource\RelationManagers;
use App\Filament\Resources\Animal\AdoptionResource\Widgets\AdoptionStatsOverview;
use App\Models\Adoption\Adoption;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
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

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
                ->color('primary'),

                TextColumn::make('user.name')
                ->label('Adopter')
                ->searchable()
                ->sortable(),

                TextColumn::make('dog.dog_name')->searchable()->label('Dog Name')->sortable()
                ->description(fn (Adoption $record): string => $record?->dog?->breed?->breed_name)->wrap(),

                TextColumn::make('status')->label('Status')->toggleable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'primary',
                    'approved' => 'success',
                    'rejected' => 'danger',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'pending' => 'heroicon-o-clock',
                    'approved' => 'heroicon-o-hand-thumb-up',
                    'rejected' => 'heroicon-o-x-circle',
                })
                ->formatStateUsing(fn (string $state) => ucfirst($state)),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    // ->before(function ($query) {
                    //     // Eager load the dog relationship to avoid N+1 issue
                    //     $query->with('dog');
                    // })
                    ->after(function (Adoption $record) {
                        $dog = $record->dog;
                        if ($dog) {
                            $dog->status = 'available';
                            $dog->save();
                        }
                    }),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getWidgets(): array
    {
        return [
            AdoptionStatsOverview::class
        ];
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


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
               ComponentsSection::make('Adoption Information')
               ->icon('heroicon-o-information-circle')
               ->schema([
                    Group::make([
                        TextEntry::make('dog.dog_name')->label('Dog Name')->size(TextEntrySize::Large)
                        ->formatStateUsing(function(Model $record): string {
                                $breed = $record?->dog?->breed?->breed_name;
                                $dog = $record?->dog?->dog_name;
                            return $dog . ' (' . $breed . ')';
                        }),

                        ImageEntry::make('dog.first_dog_image')->label('')->square(),
                    ])->columnSpan(1),
                   Group::make([
                        TextEntry::make('adoption_number')->label('Adoption #')->size(TextEntrySize::Large)->badge()->color('primary'),
                        TextEntry::make('user.name')->label('Adopter')->size(TextEntrySize::Large),
                        TextEntry::make('request_date')->label('Request Date')->size(TextEntrySize::Large),
                        TextEntry::make('status')->label('Status')->size(TextEntrySize::Large)->badge()->color(fn (string $state): string => match ($state) {
                            'pending' => 'primary',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'pending' => 'heroicon-o-clock',
                            'approved' => 'heroicon-o-hand-thumb-up',
                            'rejected' => 'heroicon-o-x-circle',
                        })
                        ->formatStateUsing(fn (string $state) => ucfirst($state)),
                    ])
                    ->columnSpan(2)
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                   ])
               ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
               ])

            ]);
    }
}
