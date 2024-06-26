<?php

namespace App\Filament\Resources\Donation;

use App\Filament\Resources\Donation\DonationResource\Pages;
use App\Filament\Resources\Donation\DonationResource\RelationManagers;
use App\Models\Donation\Donation;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Donation Details')
                ->description('All fields are required')
                ->schema([
                    Select::make('user_id')
                    ->required()
                    ->label('Donor')
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->preload()
                    ->optionsLimit(6)
                    ->required()
                    ->searchable()
                    ->searchDebounce(1200),
                    // ->getSearchResultsUsing(function (Builder $query, string $search): Builder {
                    //     return $query->where('name', 'like', "%{$search}%");
                    // }),

                    TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal'),

                    Select::make('donation_type')
                    ->required()
                    ->options([
                        'one-time' => 'One Time',
                        'monthly' => 'Monthly',
                    ])
                    ->native('false'),

                    Toggle::make('is_verified')
                    ->required()
                    ->onIcon('heroicon-m-check-circle')
                    ->offIcon('heroicon-m-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false),

                    RichEditor::make('donation_message')
                    ->required()
                    ->label('Message')->columnSpanFull(),

                    DatePicker::make('donation_date')
                    ->required()
                    ->native(false)
                    ->default(now())
                ])
                ->columns([
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
                TextColumn::make('user.name')->label('Donor')->searchable()->sortable()->weight('bold'),
                TextColumn::make('amount')->label('Amount')->searchable()->sortable(),
                TextColumn::make('donation_type')->sortable(),
                TextColumn::make('donation_message')->html()->sortable()->wrap()->limit(50),
                IconColumn::make('is_verified')->boolean()
                ->trueIcon('heroicon-m-check-circle')
                ->falseIcon('heroicon-m-x-circle'),
                TextColumn::make('donation_date')->date()->sortable(),
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
                ->label(__('New Donation')),
            ])
            ->emptyStateIcon('heroicon-o-gift')
            ->emptyStateHeading('No donations are created');
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
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make([
                    TextEntry::make('user.name')->weight('bold'),
                    TextEntry::make('amount'),
                    TextEntry::make('donation_type'),
                    TextEntry::make('donation_message')->markdown()->columnSpanFull(),
                    TextEntry::make('donation_date'),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])
            ]);

    }
}
