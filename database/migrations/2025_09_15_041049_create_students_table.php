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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('grade_level');
            $table->string('section');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
            
            // Guardian fields
            $table->enum('guardian_type', ['mother','father','guardian'])->nullable();
            $table->string('guardian_first_name')->nullable();
            $table->string('guardian_last_name')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->string('guardian_email')->nullable();
            
            // Mother fields
            $table->boolean('mother_enabled')->default(false);
            $table->string('mother_first_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_contact')->nullable();
            
            // Father fields
            $table->boolean('father_enabled')->default(false);
            $table->string('father_first_name')->nullable();
            $table->string('father_last_name')->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_contact')->nullable();
            
            // Guardian fields (additional)
            $table->boolean('guardian_enabled')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
