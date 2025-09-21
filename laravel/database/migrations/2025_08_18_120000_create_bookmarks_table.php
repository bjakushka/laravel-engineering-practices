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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()
                ->comment('Primary key, auto-incrementing user ID');

            $table->unsignedBigInteger('user_id')
                ->nullable(false)
                ->comment('Foreign key to the users table, who created the bookmark');

            $table->text('url')
                ->nullable(false)
                ->comment('URL of the bookmark, cannot be null');

            $table->string('title', 255)
                ->nullable(false)
                ->comment('Title of the bookmark, cannot be null');

            $table->boolean('is_read')
                ->default(false)
                ->nullable(false)
                ->comment('Indicates if the bookmark has been read, default is false, cannot be null');

            $table->timestamp('read_at')
                ->nullable()
                ->comment('Timestamp when the bookmark was read, can be null if not read yet');

            $table->timestamps();

            // note: there is an issue with primary key in sqlite, so we use only autoIncrement
            // $table->primary('id', 'pk_bookmarks');
            $table->foreign('user_id', 'fk_bookmarks_user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
