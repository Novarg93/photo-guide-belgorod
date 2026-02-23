<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $fillable = [
        'page_key',
        'seo_title',
        'seo_description',
    ];

    public const PAGE_WELCOME = 'welcome';

    public const PAGE_CATALOG = 'catalog';

    public const PAGE_LOCATIONS = 'locations';

    public static function pageOptions(): array
    {
        return [
            self::PAGE_WELCOME => 'Welcome page',
            self::PAGE_CATALOG => 'Catalog page',
            self::PAGE_LOCATIONS => 'Locations page',
        ];
    }
}
