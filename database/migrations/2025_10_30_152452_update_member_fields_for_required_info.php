<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMemberFieldsForRequiredInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            // Add new fields
            $table->integer('age')->after('name')->nullable();
            $table->integer('height_ft')->after('age')->nullable();
            $table->integer('height_in')->after('height_ft')->nullable();
            $table->decimal('weight_kg', 5, 2)->after('height_in')->nullable();
            $table->boolean('opf_residence')->after('weight_kg')->nullable();
        });
    
        Schema::table('mst_members', function (Blueprint $table) {
            // Remove unwanted fields
            $table->dropColumn([
                'DOB',
                'email',
                'emergency_contact',
                'proof_name',
                'proof_photo',
                'photo',
                'aim',
                'source',
                'occupation',
                'pin_code'
            ]);
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
            // Remove fields added in up()
            $table->dropColumn(['age', 'height_ft', 'height_in', 'weight_kg', 'opf_residence']);
        });
    
        Schema::table('mst_members', function (Blueprint $table) {
            // Add back fields removed in up()
            $table->date('DOB')->nullable();
            $table->string('email', 50)->nullable();
            $table->string('emergency_contact', 11)->nullable();
            $table->string('proof_name', 50)->nullable();
            $table->string('proof_photo', 50)->nullable();
            $table->string('photo', 50)->nullable();
            $table->string('aim', 50)->nullable();
            $table->string('source', 50)->nullable();
            $table->string('occupation', 50)->nullable();
            $table->integer('pin_code')->nullable();
        });
    }
}
