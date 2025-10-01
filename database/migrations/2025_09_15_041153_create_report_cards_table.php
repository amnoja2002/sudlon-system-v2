<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->string('school_year');
            $table->string('semester'); // 1st Quarter, 2nd Quarter, etc.
            $table->json('grades'); // Store all subject grades as JSON
            $table->decimal('average', 5, 2)->nullable();
            $table->string('remarks')->nullable(); // Passed, Failed, etc.
            $table->text('teacher_comments')->nullable();
            $table->foreignId('generated_by')->constrained('users');
            $table->string('scope')->default('mine'); // 'mine' | 'all'
            $table->timestamp('generated_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
