<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each table, we update existing rows to be JSON compatible with Spatie Translatable
        $tables = [
            'blog_categories' => ['title'],
            'blog' => ['title', 'description', 'content'],
            'sliders' => ['name', 'description'],
            'slider_items' => ['title', 'description'],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            
            $records = DB::table($table)->get();
            foreach ($records as $record) {
                $updateData = [];
                foreach ($columns as $column) {
                    $val = $record->$column;
                    // Check if it's already a JSON string to prevent double encoding
                    if (!is_null($val) && json_decode($val, true) === null) {
                        $updateData[$column] = json_encode(['vi' => $val], JSON_UNESCAPED_UNICODE);
                    }
                }
                if (!empty($updateData)) {
                    DB::table($table)->where('id', $record->id)->update($updateData);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'blog_categories' => ['title'],
            'blog' => ['title', 'description', 'content'],
            'sliders' => ['name', 'description'],
            'slider_items' => ['title', 'description'],
        ];
        
        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $records = DB::table($table)->get();
            foreach ($records as $record) {
                $updateData = [];
                foreach ($columns as $column) {
                    $val = $record->$column;
                    if (!is_null($val)) {
                        $decoded = json_decode($val, true);
                        if (is_array($decoded) && isset($decoded['vi'])) {
                            $updateData[$column] = $decoded['vi'];
                        }
                    }
                }
                if (!empty($updateData)) {
                    DB::table($table)->where('id', $record->id)->update($updateData);
                }
            }
        }
    }
};
