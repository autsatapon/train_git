<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
class CreateCreditCardsTable extends Migration{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('credit_cards', function (Blueprint $table){
            $table->increments('id');
            $table->string('card_number', 16); // xxxxxxxxxxxx1111, 123456xxxxxxxxxx, 123456xxxxxx1234
            $table->string('card_expiry_date', 7); // 12-2020
            $table->string('card_type', 20); // 001=VISA, 002=MASTER, etc.
            $table->string('payment_token', 30); // e.g. 3644783643210170561946
            $table->bigInteger('member_id')->unsigned();
            $table->foreign('member_id')->references('id')->on('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('credit_cards', function (Blueprint $table){
            $table->dropForeign('credit_cards_member_id_foreign');
        });
        Schema::drop('credit_cards');
    }
}
