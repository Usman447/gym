<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTrnWhatsappMessageHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('trn_whatsapp_message_history')) {
            Schema::create('trn_whatsapp_message_history', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('member_id')->unsigned();
                $table->integer('subscription_id')->unsigned()->nullable();
                $table->tinyInteger('reminder_number')->comment('1 = First reminder, 2 = Second reminder, 3 = Third reminder');
                $table->date('subscription_end_date')->comment('The end date of the subscription when reminder was sent');
                $table->text('message');
                $table->string('phone_number', 20);
                $table->string('status', 50)->default('sent')->comment('sent, failed, pending');
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at');
                $table->timestamps();

                $table->index('member_id');
                $table->index('subscription_id');
                $table->index('reminder_number');
                $table->index('subscription_end_date');
                $table->index('sent_at');
                $table->index('status');
            });

            // Add foreign keys using raw SQL to match existing table structure
            try {
                DB::statement('ALTER TABLE trn_whatsapp_message_history ADD CONSTRAINT trn_whatsapp_message_history_member_id_foreign FOREIGN KEY (member_id) REFERENCES mst_members(id) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }

            try {
                DB::statement('ALTER TABLE trn_whatsapp_message_history ADD CONSTRAINT trn_whatsapp_message_history_subscription_id_foreign FOREIGN KEY (subscription_id) REFERENCES trn_subscriptions(id) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_whatsapp_message_history');
    }
}

