<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        $usedSlugs = [];

        DB::table('photographers')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get()
            ->each(function (object $photographer) use (&$usedSlugs): void {
                $baseSlug = Str::slug((string) $photographer->name);

                if ($baseSlug === '') {
                    $baseSlug = 'photographer';
                }

                $slug = $baseSlug;
                $counter = 1;

                while (in_array($slug, $usedSlugs, true)) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $usedSlugs[] = $slug;

                DB::table('photographers')
                    ->where('id', $photographer->id)
                    ->update(['slug' => $slug]);
            });

        Schema::table('photographers', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
