<?php

namespace App\Filament\Resources\Animal\DogResource\Pages;

use App\Filament\Resources\Animal\DogResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageDogMedicalRecord extends ManageRelatedRecords
{

    protected static string $resource = DogResource::class;

    protected static string $relationship = 'medicalRecords';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Medical Records';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
               TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('veterinarian')
                ->required()
                ->maxLength(255),

                DatePicker::make('record_date')
                ->required()
                ->default(now())
                ->native(false),

                RichEditor::make('description')
                ->maxLength(65535)->columnSpanFull()
            ])->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2
              ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dog_id')
            ->columns([
                Tables\Columns\TextColumn::make('veterinarian')->weight('bold'),
                Tables\Columns\TextColumn::make('type')->markdown(),
                Tables\Columns\TextColumn::make('description')
                ->markdown()
                ->wrap()
                ->limit(255),
                Tables\Columns\TextColumn::make('record_date')
                ,
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
               ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
               ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
              Section::make()->schema([
                  TextEntry::make('type')->label('Type')->badge()->color('primary'),
                  TextEntry::make('veterinarian')->label('Veterinarian')->size('bold')->badge()->color('success'),
                  TextEntry::make('record_date')->label('Record Date'),
                  TextEntry::make('description')->label('Description')->columnSpanFull()->markdown(),
              ])->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2
              ])
            ]);
    }
}
