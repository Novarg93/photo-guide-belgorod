<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('seo_title')->nullable()->after('description');
            $table->text('seo_description')->nullable()->after('seo_title');
        });

        $locations = DB::table('locations')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get();

        $usedSlugs = [];

        foreach ($locations as $location) {
            $baseSlug = Str::slug((string) $location->name);
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'location';
            $slug = $baseSlug;
            $counter = 1;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            $usedSlugs[] = $slug;

            DB::table('locations')
                ->where('id', $location->id)
                ->update([
                    'slug' => $slug,
                ]);
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug',
                'description',
                'seo_title',
                'seo_description',
            ]);
        });
    }
};
