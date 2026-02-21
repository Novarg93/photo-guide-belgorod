<?php

namespace App\Filament\Resources\Photos\Schemas;

use App\Models\Example;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PhotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->disabled(fn (Get $get): bool => filled($get('example_id')))
                    ->dehydrated()
                    ->rule(function (Get $get): \Closure {
                        return function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                            $exampleId = $get('example_id');

                            if (blank($exampleId)) {
                                return;
                            }

                            $exampleCategoryId = Example::query()
                                ->whereKey($exampleId)
                                ->value('category_id');

                            if ($exampleCategoryId !== null && (int) $value !== (int) $exampleCategoryId) {
                                $fail('Category must match the selected example category.');
                            }
                        };
                    }),
                Select::make('example_id')
                    ->relationship('example', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        if (blank($state)) {
                            return;
                        }

                        $exampleCategoryId = Example::query()
                            ->whereKey($state)
                            ->value('category_id');

                        if ($exampleCategoryId !== null) {
                            $set('category_id', (string) $exampleCategoryId);
                        }
                    }),
                FileUpload::make('path')
                    ->disk('public')
                    ->directory('photos')
                    ->image()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
                Select::make('source_type')
                    ->options([
                        'own' => 'own',
                        'permission' => 'permission',
                        'stock' => 'stock',
                    ])
                    ->required()
                    ->helperText('own: created by project, permission: approved by owner, stock: licensed library.')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                        if (blank($get('license'))) {
                            $set('license', $state);
                        }
                    }),
                TextInput::make('license')
                    ->required()
                    ->helperText('Examples: own, permission, stock, stock-commercial')
                    ->placeholder('e.g. stock')
                    ->maxLength(255),
                TextInput::make('source_url')
                    ->url()
                    ->helperText('Required for permission unless you provide a permission note.')
                    ->maxLength(255)
                    ->required(fn (Get $get): bool => $get('source_type') === 'permission' && blank($get('permission_note'))),
                TextInput::make('author_name')
                    ->maxLength(255),
                Textarea::make('permission_note')
                    ->helperText('Required for permission unless you provide a source URL.')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(fn (Get $get): bool => $get('source_type') === 'permission' && blank($get('source_url'))),
                Toggle::make('is_active')
                    ->default(false),
            ]);
    }
}
