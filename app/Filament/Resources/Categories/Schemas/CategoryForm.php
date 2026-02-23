<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                        if (($get('slug') ?? '') !== Str::slug((string) $old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    }),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Toggle::make('is_active')
                    ->default(true),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                Repeater::make('filter_groups')
                    ->label('Filter groups')
                    ->columnSpanFull()
                    ->default([])
                    ->collapsible()
                    ->cloneable()
                    ->reorderableWithButtons()
                    ->addActionLabel('Add filter group')
                    ->schema([
                        TextInput::make('name')
                            ->label('Group title')
                            ->required()
                            ->maxLength(255),
                        Repeater::make('options')
                            ->label('Filter options')
                            ->required()
                            ->minItems(1)
                            ->default([])
                            ->collapsible()
                            ->cloneable()
                            ->reorderableWithButtons()
                            ->addActionLabel('Add option')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Option title')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),
                TextInput::make('seo_title')
                    ->maxLength(255),
                Textarea::make('seo_description')
                    ->rows(3),
            ]);
    }
}
