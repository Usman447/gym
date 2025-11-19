<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeCreditBalanceToIntegerInMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change credit_balance from decimal to integer using raw SQL
        DB::statement('ALTER TABLE mst_members MODIFY COLUMN credit_balance INT(11) DEFAULT 0 COMMENT "Member credit balance from overpaid invoices (positive = credit, negative = due)"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert credit_balance from integer back to decimal
        DB::statement('ALTER TABLE mst_members MODIFY COLUMN credit_balance DECIMAL(10,2) DEFAULT 0 COMMENT "Member credit balance from overpaid invoices (positive = credit, negative = due)"');
    }
}
