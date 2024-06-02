<?php

namespace App\Filament\Resources\Post\PostResource\Pages;

use App\Filament\Resources\Post\PostResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Is Featured' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->icon('heroicon-m-star'),
            'Not Published' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false))
                ->icon('heroicon-m-document-text'),
        ];
    }
}
