<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'student_id')) {
                // Drop foreign key if it exists, then the column
                try {
                    $table->dropForeign(['student_id']);
                } catch (\Throwable $e) {
                    // Ignore if FK name differs or doesn't exist
                }
                $table->dropColumn('student_id');
            }

            if (Schema::hasColumn('subjects', 'code')) {
                $table->dropColumn('code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'student_id')) {
                $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('subjects', 'code')) {
                $table->string('code')->nullable();
            }
        });
    }
};


