<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('parent_access_requests') && !Schema::hasColumn('parent_access_requests', 'student_id')) {
            Schema::table('parent_access_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('student_id')->nullable()->after('subject_id');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('parent_student_accesses')) {
            Schema::create('parent_student_accesses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('classroom_id')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->unsignedBigInteger('approved_by');
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');

                $table->unique(['parent_id', 'student_id', 'subject_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('parent_access_requests') && Schema::hasColumn('parent_access_requests', 'student_id')) {
            Schema::table('parent_access_requests', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            });
        }

        Schema::dropIfExists('parent_student_accesses');
    }
};


