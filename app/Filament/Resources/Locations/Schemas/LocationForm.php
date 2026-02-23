<?php

namespace App\Filament\Resources\Locations\Schemas;

use App\Models\Category;
use App\Support\CategoryFilterSchema;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LocationForm
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
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('filter_option_keys', []);
                    }),
                CheckboxList::make('filter_option_keys')
                    ->label('Filter options')
                    ->helperText('Choose filter options available in the selected category.')
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
                                $fail('Selected filter options must belong to the selected category.');
                            }
                        };
                    })
                    ->columnSpanFull(),
                FileUpload::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->directory('locations')
                    ->image()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
                FileUpload::make('example_photo_paths')
                    ->label('Photo session examples')
                    ->disk('public')
                    ->directory('locations/examples')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('seo_title')
                    ->label('SEO title')
                    ->maxLength(255)
                    ->helperText('Recommended length: up to 60 characters.')
                    ->columnSpanFull(),
                Textarea::make('seo_description')
                    ->label('SEO description')
                    ->rows(3)
                    ->helperText('Recommended length: up to 160 characters.')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
