<?php

namespace App\Filament\Resources\Examples\Schemas;

use App\Models\Category;
use App\Support\CategoryFilterSchema;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ExampleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('filter_option_keys', []);
                    })
                    ->required(),
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
                Toggle::make('is_active')
                    ->default(true),
                Textarea::make('summary')
                    ->rows(4)
                    ->columnSpanFull(),
                CheckboxList::make('filter_option_keys')
                    ->label('Preset filters')
                    ->helperText('These filters will be applied when this preset is selected.')
                    ->columns(2)
                    ->searchable()
                    ->visible(fn (Get $get): bool => filled($get('category_id')))
                    ->options(function (Get $get): array {
                        $categoryId = $get('category_id');

                        if (blank($categoryId)) {
                            return [];
                        }

                        $category = Category::query()
                            ->select(['id', 'filter_groups'])
                            ->find($categoryId);

                        return CategoryFilterSchema::flattenOptions($category?->filter_groups);
                    })
                    ->rule(function (Get $get): \Closure {
                        return function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                            $categoryId = $get('category_id');

                            if (blank($categoryId)) {
                                return;
                            }

                            $category = Category::query()
                                ->select(['id', 'filter_groups'])
                                ->find($categoryId);

                            $sanitized = CategoryFilterSchema::filterSelected($category?->filter_groups, $value);

                            if (count($sanitized) !== count(is_array($value) ? $value : [])) {
                                $fail('Selected preset filters must belong to the selected category.');
                            }
                        };
                    })
                    ->columnSpanFull(),
                TextInput::make('mood')
                    ->maxLength(255),
                TextInput::make('location_hint')
                    ->maxLength(255),
                TextInput::make('season_hint')
                    ->maxLength(255),
                TextInput::make('clothing_hint')
                    ->maxLength(255),
            ]);
    }
}
