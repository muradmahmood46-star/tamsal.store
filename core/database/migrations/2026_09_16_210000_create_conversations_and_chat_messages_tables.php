<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsAndChatMessagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('item_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->index(); // Buyer
                $table->unsignedBigInteger('vendor_id')->default(0)->index(); // Seller / Store User ID (0 for admin)
                $table->text('last_message')->nullable();
                $table->timestamp('last_message_at')->nullable();
                $table->integer('user_unread_count')->default(0);
                $table->integer('vendor_unread_count')->default(0);
                $table->tinyInteger('deleted_by_user')->default(0);
                $table->tinyInteger('deleted_by_vendor')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('conversation_id')->index();
                $table->enum('sender_type', ['user', 'vendor', 'admin'])->default('user');
                $table->unsignedBigInteger('sender_id');
                $table->text('message');
                $table->string('attachment')->nullable();
                $table->tinyInteger('is_read')->default(0);
                $table->tinyInteger('deleted_by_user')->default(0);
                $table->tinyInteger('deleted_by_vendor')->default(0);
                $table->timestamps();

                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('conversations');
    }
}
