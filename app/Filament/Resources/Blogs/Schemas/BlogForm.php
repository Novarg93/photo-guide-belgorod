<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                        if (($get('slug') ?? '') !== Str::slug((string) $old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('published_at')
                    ->seconds(false),
                Toggle::make('is_active')
                    ->default(true),
                FileUpload::make('cover_image')
                    ->label('Cover image')
                    ->disk('public')
                    ->directory('blogs')
                    ->image()
                    ->columnSpanFull(),
                Textarea::make('excerpt')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->rows(16)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('seo_title')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('seo_description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
