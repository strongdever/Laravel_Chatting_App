<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Musonza\Chat\ConfigurationManager;

class CreateChatTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ConfigurationManager::CONVERSATIONS_TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('private')->default(true);
            $table->boolean('direct_message')->default(false);
            $table->text('data')->nullable();
            $table->timestamps();
        });

        Schema::create(ConfigurationManager::PARTICIPATION_TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('conversation_id')->unsigned();
            $table->bigInteger('messageable_id')->unsigned();
            $table->string('messageable_type');
            $table->text('settings')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'messageable_id', 'messageable_type'], 'participation_index');

            $table->foreign('conversation_id')
                ->references('id')
                ->on(ConfigurationManager::CONVERSATIONS_TABLE)
                ->onDelete('cascade');
        });

        Schema::create(ConfigurationManager::MESSAGES_TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->text('body');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on(ConfigurationManager::PARTICIPATION_TABLE)
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConfigurationManager::MESSAGES_TABLE);
        Schema::dropIfExists(ConfigurationManager::PARTICIPATION_TABLE);
        Schema::dropIfExists(ConfigurationManager::CONVERSATIONS_TABLE);
    }
}
