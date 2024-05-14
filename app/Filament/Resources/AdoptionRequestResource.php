<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdoptionRequestResource\Pages;
use App\Filament\Resources\AdoptionRequestResource\RelationManagers;
use App\Models\AdoptionRequest;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;

class AdoptionRequestResource extends Resource
{
    protected static ?string $model = AdoptionRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Adoption Request Details')
                ->description('All fields are required')
                ->schema([
                    Select::make('user_id')
                    ->relationship(name:'user', titleAttribute:'name')
                    ->searchable()->native(false)->optionsLimit(6)->required()->label('Adopter')->preload(),
                    Select::make('dog_id')
                    ->relationship(name:'dog', titleAttribute:'dog_name')
                    ->searchable()->native(false)->optionsLimit(6)->required()->label('Dog')->preload(),
                    MarkdownEditor::make('reason')->columnSpanFull()->required(),
                    Toggle::make('is_approved')->inline(false)->columns(1)->onColor('success')
                    ->offColor('danger'),
                    DatePicker::make('request_date')->date()->native(false)->required()->default(now())->label('Requested Date'),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->sortable()->searchable()->label('Adapter'),
                TextColumn::make('dog.dog_name')->sortable()->searchable()
                ->description(
                    fn (AdoptionRequest $record): string => $record?->dog?->breed?->breed_name
                )->label('Requested Dog'),
                ImageColumn::make('dog.dog_img')->circular()->label('Dog Image'),
                TextColumn::make('reason')->wrap()->limit(50)->label('Reason for Adoption'),
                ToggleColumn::make('is_approved')->label('Is Approved?'),
                TextColumn::make('request_date')->date()->label('Requested Date'),
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
                ->label(__('Create Adoption Request')),
            ])
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading('No request have been created');
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
