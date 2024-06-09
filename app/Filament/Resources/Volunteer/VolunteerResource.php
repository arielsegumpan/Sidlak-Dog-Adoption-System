<?php

namespace App\Filament\Resources\Volunteer;

use App\Filament\Resources\Volunteer\VolunteerResource\Pages;
use App\Filament\Resources\Volunteer\VolunteerResource\RelationManagers;
use App\Models\User;
use App\Models\Volunteer\Volunteer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VolunteerResource extends Resource
{
    protected static ?string $model = Volunteer::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Volunteer Details')
                ->schema([
                    Select::make('user_id')
                    ->required()
                    ->relationship(name:'user', titleAttribute:'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->optionsLimit(6),
                    // ->options(function (Builder $query) {
                    //     return $query->whereHas('roles', function (Builder $query) {
                    //         $query->where('name', 'Volunteer');
                    //     })->get()->pluck('name', 'id');
                    // })

                    Select::make('role')
                    ->required()
                    ->options([
                        'dog_walking' => 'Dog Walking',
                        'event_assistance' => 'Event Assistance',
                        'admin_support' => 'Admin Support',
                        'community_outreach' => 'Community Outreach',
                    ])->native(false),

                    RichEditor::make('reason')
                    ->required()
                    ->placeholder('Reason for joining')
                    ->columnSpanFull(),

                    ToggleButtons::make('status')
                    ->required()
                    ->default('active')->inline()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->icons([
                        'active' => 'heroicon-o-check-circle',
                        'inactive' => 'heroicon-o-x-circle',
                    ])
                    ->colors([
                        'active' => 'success',
                        'inactive' => 'danger',
                    ]),

                    DatePicker::make('joined_date')
                    ->required()
                    ->default(now())
                    ->native(false)

                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                    'default' => 2
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->searchable()->sortable()
                ->formatStateUsing(function (string $state): string {
                    return ucwords(str_replace('_', ' ', $state));
                }),
                Tables\Columns\TextColumn::make('reason')->wrap()->limit(60)->html(),
                Tables\Columns\TextColumn::make('status')->sortable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->icon(fn (string $state): string => match ($state) {
                    'active' => 'heroicon-o-check-circle',
                    'inactive' => 'heroicon-o-x-circle',
                })->formatStateUsing(fn (string $state): string => ucwords($state)),
                Tables\Columns\TextColumn::make('joined_date')->searchable()->sortable(),
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
                ->label(__('New Volunteer')),
            ])
            ->emptyStateIcon('heroicon-o-heart')
            ->emptyStateHeading('No volunteers are registered');
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
            'index' => Pages\ListVolunteers::route('/'),
            'create' => Pages\CreateVolunteer::route('/create'),
            'edit' => Pages\EditVolunteer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
