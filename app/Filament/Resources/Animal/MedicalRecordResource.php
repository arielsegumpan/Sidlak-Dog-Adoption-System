<?php

namespace App\Filament\Resources\Animal;

use App\Filament\Resources\Animal\MedicalRecordResource\Pages;
use App\Filament\Resources\Animal\MedicalRecordResource\RelationManagers;
use App\Models\Animal\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        Select::make('dog_id')->relationship('dog', 'dog_name')->required()
                        ->native(false)
                        ->searchable(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('dog.first_dog_image')->square(),
                TextColumn::make('dog.dog_name')->label('Name & Breed')->description(fn (MedicalRecord $record): string => $record?->dog?->breed?->breed_name)->wrap()->sortable()->searchable(),
                TextColumn::make('type')->searchable()->sortable()->wrap()->limit(40),
                TextColumn::make('description')->wrap()->limit(60)->html(),
                TextColumn::make('veterinarian')->wrap()->limit(60)->html(),
                TextColumn::make('record_date')
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
}
