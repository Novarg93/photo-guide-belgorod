<?php

namespace App\Filament\Resources\PageSeos\Tables;

use App\Models\PageSeo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageSeosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page_key')
                    ->label('Page')
                    ->formatStateUsing(fn (string $state): string => PageSeo::pageOptions()[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('seo_title')
                    ->label('SEO title')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('page_key')
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
