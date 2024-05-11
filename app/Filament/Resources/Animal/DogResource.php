<?php

namespace App\Filament\Resources\Animal;

use App\Filament\Resources\Animal\DogResource\Pages;
use App\Filament\Resources\Animal\DogResource\RelationManagers;
use App\Models\Animal\Dog;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DogResource extends Resource
{
    protected static ?string $model = Dog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Animal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dog Information')
                ->icon('heroicon-o-rectangle-stack')
                ->description('Please provide the following information about the dog.')
                ->schema([
                    TextInput::make('dog_name')->required()->maxLength(255),
                    Select::make('breed_id')->relationship('breed', 'breed_name')->required()->preload()->optionsLimit(8)->searchable()->native(false),
                    TextInput::make('age')->required()->numeric()->minValue(0),
                    ColorPicker::make('color')->required()->default('white'),
                    Select::make('dog_size')->required()->options([
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'large' => 'Large',])->native(false),
                    CheckboxList::make('adoption_status')
                    ->required()->options([
                        'available' => 'Available',
                        'adopted' => 'Adopted',
                    ])->columns(2),
                    MarkdownEditor::make('dog_description')->required()->columnSpanFull(),

                ])->columns([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 2,
                ]),

                Section::make('Dog Profile Image')
                ->schema([
                    FileUpload::make('dog_image')->image()->required()->imageEditor() ->imageEditorAspectRatios([
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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
}
