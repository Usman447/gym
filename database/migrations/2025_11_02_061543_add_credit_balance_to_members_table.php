<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreditBalanceToMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            $table->decimal('credit_balance', 10, 2)->default(0)->after('status')->comment('Member credit balance from overpaid invoices (positive = credit, negative = due)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            $table->dropColumn('credit_balance');
        });
    }
}
