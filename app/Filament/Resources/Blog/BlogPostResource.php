<?php

namespace App\Filament\Resources\Blog;

use App\Filament\Resources\Blog\BlogPostResource\Pages;
use App\Filament\Resources\Blog\BlogPostResource\RelationManagers;
use App\Models\Blog\BlogPost;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'News and Events';

    protected static ?string $label = 'Post';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Hidden::make('author_id')->default(auth()->user()->id),
                        TextInput::make('post_title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('post_slug', Str::slug($state))),
                            // ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('post_slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(BlogPost::class, 'post_slug', ignoreRecord: true),

                        RichEditor::make('post_content')
                            ->required()
                            ->columnSpan('full'),

                        // Select::make('category_id')
                        //     ->relationship(name:'category', titleAttribute:'category_name')
                        //     ->searchable()
                        //     ->required()
                        //     ->native(false)
                        //     ->optionsLimit(8)
                        //     ->preload()
                        //     ->createOptionForm(
                        //         [
                        //             TextInput::make('category_name')
                        //             ->required()
                        //             ->maxLength(255)
                        //             ->live(onBlur: true)
                        //             ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        //             TextInput::make('slug')
                        //                 ->disabled()
                        //                 ->dehydrated()
                        //                 ->required()
                        //                 ->maxLength(255)
                        //                 ->unique(Category::class, 'slug', ignoreRecord: true),

                        //             MarkdownEditor::make('description')
                        //                 ->columnSpan('full'),
                        //         ]
                        //     )->createOptionAction(function (Action $action) {
                        //         return $action
                        //             ->modalHeading('Create category')
                        //             ->modalSubmitActionLabel('Create category');
                        //     }),

                        // Select::make('tags')
                        // ->multiple()->native(false)->searchable()->preload()->optionsLimit(6)
                        // ->required()->relationship(name:'tags', titleAttribute: 'tag_name')
                        // ->createOptionForm([
                        //     TextInput::make('tag_name')->required()->maxLength(255)
                        //     ->live(onBlur: true)->unique(Tag::class, 'tag_name', ignoreRecord: true)
                        //     ->afterStateUpdated(fn (Set $set, ?string $state) => $set('tag_slug', Str::slug($state))),
                        //     TextInput::make('tag_slug')
                        //     ->label('Slug')
                        //     ->disabled()
                        //     ->dehydrated()
                        //     ->required()
                        //     ->maxLength(255)
                        //     ->unique(Tag::class, 'tag_slug', ignoreRecord: true),
                        //     Textarea::make('tag_description')
                        //     ->label('Description')
                        //     ->maxLength(500)->columnSpanFull()->rows(7)->cols(10)
                        // ])->columns(2),

                        Toggle::make('is_published')
                            ->label('Is published.')
                            ->inline(false)
                            ->default(true)
                            ->required()
                    ])
                    ->columns(2),

                Section::make('Image')
                    ->label('Featured Image')
                    ->schema([
                        FileUpload::make('post_image')
                            ->label('Featured Image')
                            ->image()
                            ->hiddenLabel()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                            ])->maxSize(2048),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('post_image')->label('Featured Image'),
                TextColumn::make('post_title')->sortable()->searchable()->label('Title'),
                TextColumn::make('post_slug')->sortable()->label('Slug')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('post_content')->wrap()->limit(50)->label('Content')->html()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categories.category_name')->label('Category'),
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
                ->label(__('Create Post')),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No posts are created');
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
