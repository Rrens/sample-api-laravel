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
        Schema::create('mails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject');
            $table->uuid('user_id');
            $table->text('body');
            $table->uuid('sender_id');
            // $table->string('file_url')->nullable();
            $table->text('blob_file')->nullable();
            $table->string('file_extention')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('original_name')->nullable();
            $table->enum('mail_type', ['official', 'non_official']);
            $table->enum('is_read', ['read', 'unread']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
