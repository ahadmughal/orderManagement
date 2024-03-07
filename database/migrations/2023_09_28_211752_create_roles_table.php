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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('users')->nullable();
            $table->boolean('roles')->nullable();
            $table->boolean('permissions')->nullable();
            $table->boolean('blogs')->nullable();
            $table->boolean('profile')->nullable();
            $table->boolean('pages')->nullable();
            $table->boolean('submitted_forms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
