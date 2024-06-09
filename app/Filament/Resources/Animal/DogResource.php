<?php

namespace App\Filament\Resources\Animal;

use App\Enums\AdoptionEnum;
use App\Enums\DogEnum;
use App\Filament\Resources\Animal\DogResource\Pages;
use App\Filament\Resources\Animal\DogResource\RelationManagers;
use App\Filament\Resources\Animal\DogResource\Widgets\DogStatsOverview;
use App\Models\Animal\Breed;
use App\Models\Animal\Dog;
use App\Models\Animal\MedicalRecord;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DogResource extends Resource
{
    protected static ?string $model = Dog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Animal';

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Dog Information')
                ->icon('heroicon-o-rectangle-stack')
                ->description('Please provide the following information about the dog.')
                ->schema([
                    Group::make()
                        ->schema([
                            Section::make('Dog Details')
                        ->schema(static::getDetailsFormSchema()),

                        Section::make('Dog Photo')
                        ->schema([
                            static::getDogImagesRepeater(),
                        ]),
                    ]),

                    Forms\Components\Section::make('Medical Records')
                    // ->headerActions([
                    //     Action::make('reset')
                    //         ->modalHeading('Are you sure?')
                    //         ->modalDescription('All existing items will be removed from the order.')
                    //         ->requiresConfirmation()
                    //         ->color('danger')
                    //         ->action(fn (Forms\Set $set) => $set('items', [])),
                    // ])
                    ->schema([
                        static::getItemsRepeater(),
                    ]),

                ])->columnSpanFull(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('first_dog_image')
                ->label('Avatar')
                ->square(),
                TextColumn::make('dog_name')->label('Name & Breed')
                ->description(fn (Dog $record): string => $record?->breed?->breed_name)->wrap()->sortable()->searchable(),
                TextColumn::make('dog_description')->wrap()->limit(50)->label('Description')->html(),
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'available' => 'success',
                    'adopted' => 'danger',
                    'foster' => 'danger',
                })
                ->icon(fn (string $state): ?string => match ($state) {
                    'available' => 'heroicon-o-check-circle',
                    'adopted' => 'heroicon-o-heart',
                    'fostered' => 'heroicon-o-hand-thumb-up',
                })->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('adoption.user.name')
                ->label('Adopted by')->placeholder('---')
                ->getStateUsing(function (Model $record) {
                    if ($record->adoption && $record->adoption->status === AdoptionEnum::APPROVED->value) {
                        return $record->adoption->user->name;
                    }
                    return '---'; // or return null to use the placeholder
                })
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
                ->label(__('Create Dog')),
            ])
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateHeading('No dogs are created');
    }

    public static function getWidgets(): array
    {
        return [
            DogStatsOverview::class
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewDog::class,
            Pages\EditDog::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDogs::route('/'),
            'create' => Pages\CreateDog::route('/create'),
            'edit' => Pages\EditDog::route('/{record}/edit'),
            'view' => Pages\ViewDog::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                ComponentsSection::make('Dog Profile')
                ->description('The following information is used to display the dog on the website.')
                ->schema([
                    ImageEntry::make('first_dog_image')->label('')->width('100%')->height('270px')->square()->columnSpan(1),

                    ComponentsGroup::make()
                    ->schema([
                        TextEntry::make('dog_name')->size(TextEntrySize::Large),
                        TextEntry::make('breed.breed_name')->size(TextEntrySize::Large),
                        TextEntry::make('dog_age')->size(TextEntrySize::Large),
                        TextEntry::make('dog_gender')->formatStateUsing(fn (string $state) => ucfirst($state)),
                        TextEntry::make('status')->size(TextEntrySize::Large)->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'available' => 'success',
                            'adopted' => 'danger',
                            'fostered' => 'danger',
                        })
                        ->icon(fn (string $state): ?string => match ($state) {
                            'available' => 'heroicon-o-check-circle',
                            'adopted' => 'heroicon-o-heart',
                            'fostered' => 'heroicon-o-hand-thumb-up',
                        })->formatStateUsing(fn (string $state) => ucfirst($state)),
                        TextEntry::make('dog_size')->formatStateUsing(fn (string $state) => ucfirst($state)),
                        TextEntry::make('adoption.user.name')->label('Adopter')->placeholder('---')->formatStateUsing(fn (string $state) => ucfirst($state)),
                        TextEntry::make('adoption.adoption_number')->label('Adoption #')->placeholder('---')->formatStateUsing(fn (string $state) => ucwords($state))->badge()->color('primary'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3,
                    ])->columnSpan(2)

                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                ]),

                ComponentsSection::make('About the Dog')
                ->schema([
                    TextEntry::make('dog_description')->html()->label(''),
                ]),

                ComponentsSection::make('Medical Records')
                ->schema([
                    ComponentsGroup::make()
                    ->schema([
                        TextEntry::make('medicalRecords.type')->label('Type')->placeholder('---'),
                        TextEntry::make('medicalRecords.veterinarian')->label('Veterinarian')->placeholder('---'),
                    ])->columns([
                        'sm' => 1, 'md' => 2, 'lg' => 2
                    ]),
                    TextEntry::make('medicalRecords.description')->label('Description')->html()->placeholder('---'),
                    TextEntry::make('medicalRecords.record_date')->label('Recorded Date')->badge()->color('success')->dateTime()->placeholder('---'),
                ])->columnSpanFull()
            ]);
    }


    /** @return Builder<Dog> */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['breed', 'adoption', 'medicalRecords']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['dog_name', 'breed.breed_name','medicalRecords.type', 'adoption.adoption_number'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Dog $record */
        $details = [];

        if ($record->dog_name) {
            $details['Dog Name'] = $record->dog_name . ' (' . $record->breed->breed_name . ')';
        }

        // if ($record->breed->breed_name) {
        //     $details['Breed'] = $record->breed->breed_name;
        // }

        return $details;
    }


     /** @return Forms\Components\Component[] */
     public static function getDetailsFormSchema(): array
     {
         return [
            Group::make()
            ->schema([
                TextInput::make('dog_name')->required()->maxLength(255)->unique(ignoreRecord: true),

                Select::make('breed_id')->relationship('breed', 'breed_name')->required()->preload()->optionsLimit(8)->searchable()->native(false)
                ->createOptionForm([
                    Section::make('Breed Details')
                    ->icon('heroicon-o-information-circle')
                    ->description('All fields are required')
                    ->collapsible(true)
                    ->schema([
                        TextInput::make('breed_name')->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('breed_slug', Str::slug($state))),

                        TextInput::make('breed_slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->maxLength(255)
                        ->unique(Breed::class, 'breed_slug', ignoreRecord: true),

                        Textarea::make('breed_description')->maxLength(1024)->rows(6)->cols(20),

                        FileUpload::make('breed_image')->image()->maxSize(1024)->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                    ])
                ]),
                TextInput::make('dog_age')->required()->numeric()->minValue(0),

                Select::make('dog_size')->required()->options([
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',])->native(false),
                Select::make('dog_gender')->required()->options([
                    'male' => 'Male',
                    'female' => 'Female',
                    ])->native(false),

                ToggleButtons::make('status')
                ->required()
                ->options(DogEnum::class)
                ->inline()
                ->default('available'),
                RichEditor::make('dog_description')->required()->columnSpanFull()->maxLength(65535),
            ])->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
            ])->columnSpanFull()

         ];
     }

     public static function getItemsRepeater(): Repeater
     {
         return Repeater::make('medicalRecords')
             ->relationship()
             ->schema([
                TextInput::make('type')->required()->maxLength(255),
                TextInput::make('veterinarian')->required()->maxLength(255),
                RichEditor::make('description')->required()->columnSpanFull()->maxLength(65535),
                DatePicker::make('record_date')
                ->default(now())
                ->native(false)
                ->required(),
             ])
             ->defaultItems(0)
             ->columns([
                 'sm' => 1,
                 'md' => 2,
                 'lg' => 2,
             ])
             ->required();
     }

     public static function getDogImagesRepeater(): Repeater
     {
        return Repeater::make('dog_image')
                ->label('Photo')
                ->schema([
                    FileUpload::make('dog_image')->image()->required()->imageEditor() ->imageEditorAspectRatios([
                        null,
                        '1:1',
                        '4:3',
                    ])->maxSize(2048)->label(''),
                ])->grid([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                ])->maxItems(6);
     }
}
