<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('title')->nullable()->after('example_id');
            $table->foreignId('category_id')
                ->nullable()
                ->after('example_id')
                ->constrained()
                ->nullOnDelete();
        });

        $photos = DB::table('photos')
            ->select(['id', 'example_id', 'path'])
            ->get();

        foreach ($photos as $photo) {
            $categoryId = null;

            if ($photo->example_id !== null) {
                $categoryId = DB::table('examples')
                    ->where('id', $photo->example_id)
                    ->value('category_id');
            }

            $fallbackTitle = pathinfo((string) $photo->path, PATHINFO_FILENAME);
            if ($fallbackTitle === '') {
                $fallbackTitle = 'Photo ' . $photo->id;
            }

            DB::table('photos')
                ->where('id', $photo->id)
                ->update([
                    'category_id' => $categoryId,
                    'title' => $fallbackTitle,
                ]);
        }

        Schema::table('photos', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->index('title');
            // category_id остаётся nullable, потому что FK = nullOnDelete()
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['example_id']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->foreignId('example_id')->nullable()->change();
            $table->foreign('example_id')->references('id')->on('examples')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('photos')->whereNull('example_id')->exists()) {
            DB::table('photos')->whereNull('example_id')->delete();
        }

        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['example_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->foreignId('example_id')->nullable(false)->change();
            $table->foreign('example_id')->references('id')->on('examples')->cascadeOnDelete();
            $table->dropIndex(['title']);
            $table->dropColumn(['title', 'category_id']);
        });
    }
};
