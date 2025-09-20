<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('student_id')->constrained()->nullOnDelete();
            $table->unique(['student_id', 'subject_id', 'date'], 'attendances_student_subject_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_student_subject_date_unique');
            $table->dropConstrainedForeignId('subject_id');
        });
    }
};


