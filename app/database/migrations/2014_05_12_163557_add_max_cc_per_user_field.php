<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
class AddMaxCcPerUserField extends Migration{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('apps', function (Blueprint $table){
            $table->smallInteger('max_cc_per_user')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('apps', function (Blueprint $table){
            $table->dropColumn('max_cc_per_user');
        });
    }
}
