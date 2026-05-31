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
        Schema::create('jaringan_merge_history', function (Blueprint $table) {
            $table->id();
            $table->string('from_jaringan');
            $table->string('to_jaringan');
            $table->integer('affected_count')->default(0);
            $table->unsignedBigInteger('merged_by');
            $table->string('merged_by_name');
            $table->string('merged_by_role', 50);
            $table->boolean('is_undone')->default(false);
            $table->timestamp('undone_at')->nullable();
            $table->unsignedBigInteger('undone_by')->nullable();
            $table->string('undone_by_name')->nullable();
            $table->string('undone_by_role', 50)->nullable();
            $table->timestamps();
            
            $table->foreign('merged_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('undone_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['to_jaringan', 'created_at']);
            $table->index('is_undone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jaringan_merge_history');
    }
};
