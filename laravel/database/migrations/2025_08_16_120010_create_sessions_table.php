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
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')
                ->comment('Session ID, typically a random string');

            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->comment('Foreign key to the users table, nullable if session is not associated with a user');

            $table->string('ip_address', 45)
                ->nullable()
                ->comment('IP address of the user, nullable if not available');

            $table->text('user_agent')
                ->nullable()
                ->comment('User agent string of the user, nullable if not available');

            $table->longText('payload')
                ->comment('Serialized session data');

            $table->unsignedInteger('last_activity')
                ->comment('Timestamp of the last activity in the session');

            // indexes

            $table->primary('id', 'pk_sessions');
            $table->foreign('user_id', 'fk_sessions_user_id_users')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
