<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\Post\PostResource as PostPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostPostResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var Post */
        $record = $this->getRecord();
        return $record->title;
    }

    protected function getActions(): array
    {
        return [];
    }
}
