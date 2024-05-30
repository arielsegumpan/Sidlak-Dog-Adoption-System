<?php

namespace App\Filament\Resources\Animal;

use App\Filament\Resources\Animal\DogResource\Pages;
use App\Filament\Resources\Animal\DogResource\RelationManagers;
use App\Models\Animal\Dog;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DogResource extends Resource
{
    protected static ?string $model = Dog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Animal';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Dog Information')
            ->icon('heroicon-o-rectangle-stack')
            ->description('Please provide the following information about the dog.')
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                Select::make('breed_id')->relationship('breed', 'breed_name')->required()->preload()->optionsLimit(8)->searchable()->native(false)
                ->createOptionForm([
                    Section::make('Breed Details')
                    ->icon('heroicon-o-information-circle')
                    ->description('All fields are required')
                    ->collapsible(true)
                    ->schema([
                        TextInput::make('breed_name')->required()->maxLength(255)->unique(ignoreRecord: true),
                        Textarea::make('breed_description')->maxLength(500)->rows(6)
                        ->cols(20),
                    ])
                ]),
                TextInput::make('age')->required()->numeric()->minValue(0),
                ColorPicker::make('color')->required()->default('#ffffff'),
                Select::make('size')->required()->options([
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',])->native(false),
                // ToggleButtons::make('is_adopted')
                //     ->label('Is Adopted?')
                //     ->boolean()
                //     ->grouped()
                //     ->default(false),
                RichEditor::make('description')->required()->columnSpanFull()->maxLength(65535),

            ])->columns([
                'sm' => 1,
                'md' => 1,
                'lg' => 2,
            ]),

            Section::make('Dog Profile Image')
            ->schema([
                FileUpload::make('image')->image()->required()->imageEditor() ->imageEditorAspectRatios([
                    null,
                    '1:1',
                    '4:3',
                ])->maxSize(2048),
            ])->collapsible()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->circular()->label('Avatar'),
                TextColumn::make('name')->label('Name & Breed')
                ->description(fn (Dog $record): string => $record?->breed?->breed_name)->wrap()->sortable()->searchable(),
                TextColumn::make('description')->wrap()->limit(50)->label('Description')->html(),
                // IconColumn::make('is_adopted')
                // ->boolean()
                // ->trueIcon('heroicon-o-check-circle')
                // ->falseIcon('heroicon-o-x-circle'),
                ColorColumn::make('color')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDogs::route('/'),
            'create' => Pages\CreateDog::route('/create'),
            'edit' => Pages\EditDog::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ImageEntry::make('image')->circular(),
                TextEntry::make('name'),
                TextEntry::make('breed.breed_name'),
                TextEntry::make('age'),
                TextEntry::make('description')->html(),
                ColorEntry::make('color'),
                // IconEntry::make('is_adopted')
                // ->boolean()
            ])
            ->columns(1)
            ->inlineLabel();
    }
}
