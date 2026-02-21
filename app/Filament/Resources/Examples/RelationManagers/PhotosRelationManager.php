<?php

namespace App\Filament\Resources\Examples\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('example.title')
                    ->label('Example')
                    ->searchable(),
                TextColumn::make('source_type')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
