<?php

namespace App\Filament\Resources\Post;

use App\Filament\Resources\Post\PostResource\Pages;
use App\Filament\Resources\Post\PostResource\RelationManagers;
use App\Models\Post\Category;
use App\Models\Post\Post;
use App\Models\Post\Tag;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'News and Events';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Hidden::make('user_id')->default(auth()->user()->id),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            // ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(Post::class, 'slug', ignoreRecord: true),

                        RichEditor::make('body')
                            ->required()
                            ->columnSpan('full'),
                        TextInput::make('excerpt')->required()->maxLength(255),
                        Select::make('category_id')
                            ->relationship(name:'category', titleAttribute:'category_name')
                            ->searchable()
                            ->required()
                            ->native(false)
                            ->optionsLimit(8)
                            ->preload()
                            ->createOptionForm(
                                [
                                    TextInput::make('category_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                    TextInput::make('slug')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(Category::class, 'slug', ignoreRecord: true),

                                    MarkdownEditor::make('description')
                                        ->columnSpan('full'),
                                ]
                            )->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Create category')
                                    ->modalSubmitActionLabel('Create category');
                            }),

                        DatePicker::make('published_at')
                            ->label('Published Date')->native(false)->date()
                            ->default(now()),

                        Select::make('tags')
                        ->multiple()->native(false)->searchable()->preload()->optionsLimit(6)
                        ->required()->relationship(name:'tags', titleAttribute: 'tag_name')
                        ->createOptionForm([
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
                        ])->columns(2),

                        ToggleButtons::make('is_featured')
                            ->label('Is Featured?')
                            ->boolean()
                            ->inline()
                            ->default(false)->required(),

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
                        FileUpload::make('image')
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
                ImageColumn::make('image')
                    ->label('Featured Image'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('body')
                    ->label('Content')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->html(),
                // TextColumn::make('status')
                //     ->badge()
                //     ->getStateUsing(fn (Post $record): string => $record->published_at?->isPast() ? 'Published' : 'Draft')
                //     ->colors([
                //         'success' => 'Published',
                //     ]),

                TextColumn::make('category.category_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Published Date')
                    ->date(),

            ])
            ->filters([
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('published_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators['published_from'] = 'Published from ' . Carbon::parse($data['published_from'])->toFormattedDateString();
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators['published_until'] = 'Published until ' . Carbon::parse($data['published_until'])->toFormattedDateString();
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make('Post Details')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextEntry::make('title')->columnSpan(2),
                    TextEntry::make('slug'),
                    TextEntry::make('tags.tag_name')->columnSpanFull()->badge()->color('primary'),
                    ImageEntry::make('image'),
                    TextEntry::make('body')->columnSpanFull()->label('Content')->html(),
                    IconEntry::make('is_featured'),
                    IconEntry::make('is_published'),
                ])->columns([
                    'default' => 3,
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                    'xl' => 3,
                ])
            ]);
    }
}
