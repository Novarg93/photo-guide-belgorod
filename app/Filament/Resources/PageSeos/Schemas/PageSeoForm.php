<?php

namespace App\Filament\Resources\PageSeos\Schemas;

use App\Models\PageSeo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageSeoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('page_key')
                    ->label('Page')
                    ->options(PageSeo::pageOptions())
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                TextInput::make('seo_title')
                    ->label('SEO title')
                    ->maxLength(255)
                    ->helperText('Recommended length: up to 60 characters.')
                    ->columnSpanFull(),
                Textarea::make('seo_description')
                    ->label('SEO description')
                    ->rows(4)
                    ->helperText('Recommended length: up to 160 characters.')
                    ->columnSpanFull(),
            ]);
    }
}
