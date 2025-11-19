<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTaxAndDiscountPercentFromInvoicesAndRemoveSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_invoice', function (Blueprint $table) {
            if (Schema::hasColumn('trn_invoice', 'tax')) {
                $table->dropColumn('tax');
            }
            if (Schema::hasColumn('trn_invoice', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
        });

        // Remove settings keys related to tax and discount percentages
        DB::table('trn_settings')->whereIn('key', ['taxes', 'discounts'])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_invoice', function (Blueprint $table) {
            $table->decimal('tax', 10, 2)->default(0);
            $table->integer('discount_percent')->default(0);
        });

        // Can't restore deleted settings values reliably; leave empty
    }
}
