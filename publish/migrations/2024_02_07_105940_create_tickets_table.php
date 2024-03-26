<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->text('content');
            $table->string('source', 255);
            $table->jsonb('custom_fields')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('trello_id')->nullable();
            $table->string('trello_short_link')->nullable();
            $table->string('slack_id')->nullable();
            $table->string('page_url')->nullable();
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
}
