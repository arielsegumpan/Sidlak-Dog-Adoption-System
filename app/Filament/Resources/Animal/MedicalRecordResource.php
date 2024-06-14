<?php

namespace App\Filament\Resources\Animal;

use App\Filament\Resources\Animal\MedicalRecordResource\Pages;
use App\Filament\Resources\Animal\MedicalRecordResource\RelationManagers;
use App\Models\Animal\MedicalRecord;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Modelable;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static ?string $navigationGroup = 'Animal';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Medical Record')
                    ->description('Please provide the following information about the dog medical record/s.')
                    ->schema([
                        Select::make('dog_id')
                        ->relationship(
                            name: 'dog',
                            titleAttribute: 'dog_name',
                            modifyQueryUsing: function (Builder $query) {
                                // Eager load the 'breed' relationship
                                $query->with('breed');
                            }
                        )
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->dog_name} - {$record?->breed?->breed_name} ")
                        ->native(false)
                        ->searchable(1200)
                        ->preload()
                        ->optionsLimit(6),

                       TextInput::make('type')->required()->maxLength(255),
                       TextInput::make('veterinarian')->required()->maxLength(255),
                       DatePicker::make('record_date')->required()->date()->default(now())->native(false),
                       RichEditor::make('description')->required()->maxLength(65535)->columnSpanFull(),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('dog.dog_name')
                ->groupQueryUsing(fn (Builder $query) => $query->groupBy('dog.dog_name'))
                ->label('Name')
            ])
            ->defaultGroup('dog.dog_name')
            ->columns([
               Split::make([
                    ImageColumn::make('dog.first_dog_image')->circular()->width(70)->height(70),

                    Stack::make([
                        TextColumn::make('dog.dog_name')->label('Name')
                        ->wrap()->sortable()->searchable()->weight('bold'),
                        TextColumn::make('dog.breed.breed_name')->label('Breed')->sortable()->searchable()->size('xs')
                        ->badge()->color('success')->formatStateUsing(fn (string $state) => ucwords($state)),
                    ])->alignLeft()->space(1),


                    TextColumn::make('type')->searchable()->sortable()->wrap()->limit(40)->searchable(),
                    TextColumn::make('veterinarian')->wrap()->limit(60)->html()->searchable(),
                    TextColumn::make('description')->wrap()->limit(60)->markdown(),
                    TextColumn::make('record_date')->date()
               ])
            ])
            ->filters([
                Filter::make('record_date')
                    ->form([
                        Forms\Components\TextInput::make('veterinarian_name'),
                        Forms\Components\DatePicker::make('record_date_from')
                            ->native(false)
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('record_date_until')
                            ->native(false)
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])

                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['veterinarian_name'] ?? null,
                                fn (Builder $query, $search): Builder => $query->where('veterinarian', 'like', '%' . $search . '%'),
                            )
                            ->when(
                                $data['record_date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('record_date', '>=', $date),
                            )
                            ->when(
                                $data['record_date_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('record_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['record_date_from'] ?? null) {
                            $indicators['record_date_from'] = 'Recorded from ' . Carbon::parse($data['record_date_from'])->toFormattedDateString();
                        }
                        if ($data['record_date_until'] ?? null) {
                            $indicators['record_date_until'] = 'Recorded until ' . Carbon::parse($data['record_date_from'])->toFormattedDateString();
                        }

                        if ($data['veterinarian_name'] ?? null) {
                            $indicators['veterinarian_name'] = 'Veterinarian: ' . $data['veterinarian_name'];
                        }

                        return $indicators;
                    }),
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
                ->label(__('New Medical Record')),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('No medical records for dog are created');
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
            'index' => Pages\ListMedicalRecords::route('/'),
            'create' => Pages\CreateMedicalRecord::route('/create'),
            'edit' => Pages\EditMedicalRecord::route('/{record}/edit'),
        ];
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
               ComponentsSection::make('')
                ->schema([
                    ComponentsGroup::make([
                        TextEntry::make('dog.dog_name')->label('Dog Name')->size(TextEntrySize::Large)
                        ->formatStateUsing(function(Model $record): string {
                            $breed = $record?->dog?->breed?->breed_name;
                            $dog = $record?->dog?->dog_name;
                            return $dog . ' (' . $breed . ')';
                        }),

                        ImageEntry::make('dog.first_dog_image')->label(''),

                    ]),
                    ComponentsGroup::make([
                        TextEntry::make('veterinarian')->label('Veterinarian')->size(TextEntrySize::Large)->badge()->color('success'),
                        TextEntry::make('type')->label('Type')->badge()->color('primary'),
                        TextEntry::make('record_date')->label('Record Date')->date()->columnSpanFull(),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2
                    ]),
                    TextEntry::make('description')->label('Description')->columnSpanFull(),
                ])
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                    'xl' => 2
                ])

            ]);
    }
}
