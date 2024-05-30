<?php

namespace App\Filament\Resources\Post;

use App\Filament\Resources\Post\TagResource\Pages;
use App\Filament\Resources\Post\TagResource\RelationManagers;
use App\Models\Post\Tag;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'News and Events';

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tags')
                ->description('Create tags for post')
                ->schema([
                    TextInput::make('tag_name')->required()->maxLength(255)
                    ->live(onBlur: true)->unique(Tag::class, 'tag_name', ignoreRecord: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('tag_slug', Str::slug($state))),
                    TextInput::make('tag_slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(Tag::class, 'tag_slug', ignoreRecord: true),
                    Textarea::make('tag_description')
                    ->label('Description')
                    ->maxLength(500)->columnSpanFull()->rows(7)->cols(10)

                ])
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                    'xl' => 2
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('tag_name')->searchable()->sortable(),
               TextColumn::make('tag_slug')->toggleable(isToggledHiddenByDefault:true)->label('Slug'),
               TextColumn::make('tag_description')->label('Description')->wrap()->limit(60)

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
            ->striped()
            ->deferLoading()
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('New Tag')),
            ])
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('No tags are created');
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
