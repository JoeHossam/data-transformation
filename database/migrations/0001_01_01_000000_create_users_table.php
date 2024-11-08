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
        Schema::create('payers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->timestamps();
        });

        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->foreignId('payer_id')->constrained('payers')->onDelete('cascade');
            $table->text('authorization_notes')->nullable();
            $table->text('internal_notes')->nullable(); 
            $table->timestamps();
        });

        Schema::create('claim_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('claims')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'completed']);
            $table->timestamp('date');
            $table->timestamps();
        });

        Schema::create('mapping_rules', function (Blueprint $table) {
            $table->id();
            $table->string('internal_field')->nullable();
            $table->string('external_field');
            $table->enum('data_type', ['attribute', 'object', 'array', 'object_list']);
            $table->foreignId('parent_id')->nullable()
                  ->references('id')->on('mapping_rules')
                  ->onDelete('cascade');
            $table->integer('endpoint_id');
            $table->boolean('is_required')->default(false);
            $table->string('default_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_statuses');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('payers');
        Schema::dropIfExists('mapping_rules');
    }
};