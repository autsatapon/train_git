<?php

class CommandTableSeeder extends Seeder {

    public function run()
    {
        DB::table('commands')->delete();

        DB::table('commands')->insert(array(
		    array('name' => 'command:0', 'cron' => '* 3 * * *'), // at 3 am; DailySync
		    array('name' => 'command:1', 'cron' => '* 6,18 * * *'), // at 6.00 and 18.00; getNewLot
		    array('name' => 'command:2', 'cron' => '*/10 * * * *'), // clear hold stock every 10 minutes
		    array('name' => 'command:3', 'cron' => '30 10 * * *'), // at 10:30 get batch file of paid orders
		    array('name' => 'command:4', 'cron' => '* * * * *')
		));
    }

}