<?php

namespace App\Filament\Resources\Animal;

use App\Filament\Resources\Animal\BreedResource\Pages;
use App\Filament\Resources\Animal\BreedResource\RelationManagers;
use App\Models\Animal\Breed;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BreedResource extends Resource
{
    protected static ?string $model = Breed::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Animal';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Breed Details')
                ->icon('heroicon-o-information-circle')
                ->description('All fields are required')
                ->collapsible(true)
                ->schema([
                    TextInput::make('breed_name')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Textarea::make('breed_description')->maxLength(500)->rows(6)
                    ->cols(20),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('breed_name')->sortable()->searchable()->label('Breed'),
                TextColumn::make('breed_description')->limit(50)->wrap()->label('Description'),
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
                ->label(__('Create Breed')),
            ])
            ->emptyStateIcon('heroicon-o-rectangle-group')
            ->emptyStateHeading('No breeds are created');
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
            'index' => Pages\ListBreeds::route('/'),
            'create' => Pages\CreateBreed::route('/create'),
            'edit' => Pages\EditBreed::route('/{record}/edit'),
        ];
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('breed_name')->label('Breed'),
                TextEntry::make('breed_description')->label('Description'),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ])
            ->columns(1)
            ->inlineLabel();
    }
}
