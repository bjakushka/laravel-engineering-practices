<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()
                ->comment('Primary key, auto-incrementing user ID');

            $table->string('name', 255)->nullable(false)
                ->comment('User name, cannot be null');

            $table->string('email', 255)->nullable(false)
                ->comment('User email, must be unique and cannot be null');

            $table->string('password', 255)->nullable(false)
                ->comment('User password, cannot be null');

            $table->string('remember_token', 100)->nullable()
                ->comment('Token for "remember me" functionality, can be null');

            $table->timestamps();

            // note: there is an issue with primary key in sqlite, so we use only autoIncrement
            // $table->primary('id', 'pk_users');
            $table->unique('email', 'idx_users_email_uniq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
