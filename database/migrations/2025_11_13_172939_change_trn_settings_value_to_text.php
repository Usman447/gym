<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTrnSettingsValueToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change value column from varchar(50) to TEXT to support longer message templates
        DB::statement('ALTER TABLE trn_settings MODIFY COLUMN `value` TEXT NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to varchar(50) - Note: this may truncate existing data
        DB::statement('ALTER TABLE trn_settings MODIFY COLUMN `value` VARCHAR(50) NOT NULL');
    }
}

