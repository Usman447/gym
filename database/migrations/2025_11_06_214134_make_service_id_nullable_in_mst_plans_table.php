<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeServiceIdNullableInMstPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the foreign key constraint first
        try {
            DB::statement('ALTER TABLE mst_plans DROP FOREIGN KEY FK_mst_plans_mst_services');
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore
        }
        
        // Make service_id nullable using raw SQL
        DB::statement('ALTER TABLE mst_plans MODIFY service_id INT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make service_id not nullable again
        DB::statement('ALTER TABLE mst_plans MODIFY service_id INT NOT NULL');
        
        // Re-add the foreign key constraint
        try {
            DB::statement('ALTER TABLE mst_plans ADD CONSTRAINT FK_mst_plans_mst_services FOREIGN KEY (service_id) REFERENCES mst_services(id) ON UPDATE RESTRICT ON DELETE RESTRICT');
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
    }
}
