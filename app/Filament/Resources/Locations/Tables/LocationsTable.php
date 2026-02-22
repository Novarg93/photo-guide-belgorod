<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
