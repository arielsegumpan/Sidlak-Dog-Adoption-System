<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdoptionRequestResource\Pages;
use App\Filament\Resources\AdoptionRequestResource\RelationManagers;
use App\Models\AdoptionRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdoptionRequestResource extends Resource
{
    protected static ?string $model = AdoptionRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            ]);
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
}
