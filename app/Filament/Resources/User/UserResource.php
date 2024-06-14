<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\UserResource\Pages;
use App\Filament\Resources\User\UserResource\Pages\CreateUser;
use App\Filament\Resources\User\UserResource\Pages\EditUser;
use App\Filament\Resources\User\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Users & Roles';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),

                        DatePicker::make('email_verified_at')->required()->native(false)->default(now()),

                        // Select::make('role')
                        // ->native(false)
                        // ->required()
                        // ->options(User::ROLES),
                    ])->columnSpan(1),



                Section::make('Roles and Permission')
                ->schema([
                    Select::make('roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                   ->multiple()
                   ->preload()
                   ->optionsLimit(5)
                   ->searchable(),

                ])->columnSpan(1),


                Section::make('User Password')
                    ->schema([
                        TextInput::make('password')
                        ->rule(Password::default())
                        ->confirmed()
                        ->password()
                        ->revealable()
                        ->columnSpanFull()
                        ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                            $component->state('');
                        })
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn ($livewire) => ($livewire instanceof CreateRecord)),

                        TextInput::make('password_confirmation')
                        ->same('password')
                        ->password()
                        ->revealable()
                        ->requiredWith('password')
                    ])->columnSpanFull()

                    // ->columns([
                    //     'sm' => 1,
                    //     'md' => 2,
                    //     'lg' => 2,
                    //     'xl' => 2,
                    // ]),

                // Section::make('User New Password')
                //     ->schema([
                //         TextInput::make('new_password')
                //         ->password()
                //         ->revealable()
                //         ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                //         ->rule(Password::default()),

                //         TextInput::make('new_password_confirmation')
                //         ->password()
                //         ->revealable()
                //         ->same('new_password')
                //         ->requiredWith('new_password')

                // ])->visible(fn( $livewire) => $livewire instanceof EditUser),

            ])
            ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()
                ->label('Name & Roles'),
                TextColumn::make('roles.name'),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('email_verified_at')->date()->sortable(),
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
                ->label(__('Create User')),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No users are created');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')->label('Name')->size(TextEntrySize::Large),
                TextEntry::make('email')->label('Email'),
                // TextEntry::make('role')->label('Role')->formatStateUsing(fn (User $record): string => $record->role_label) ,
                TextEntry::make('created_at')->label('Created At'),

            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
                'default' => 2
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    // /** @return Builder<User> */
    // public static function getGlobalSearchEloquentQuery(): Builder
    // {
    //     return parent::getGlobalSearchEloquentQuery()->with(['donations', 'volunteers', 'blogPosts', 'comments']);
    // }

    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['name', 'post_slug', 'author.name', 'categories.category_name'];
    // }

    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     /** @var User $record */
    //     $details = [];

    //     if ($record->post_title) {
    //         $details['Title'] = $record->post_title;
    //     }

    //     if ($record->author) {
    //         $details['Author'] = $record->author->name;
    //     }

    //     return $details;
    // }
}
