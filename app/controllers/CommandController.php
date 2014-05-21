<?php

class CommandController extends BaseController {

	public function getIndex()
	{
		$commands = Command::all();

		return View::make('commands.index', array('commands' => $commands, 'error' => ''));
	}

	public function postStore()
	{
		$error = '';
		try {
            $cron = Cron\CronExpression::factory(Input::get('cron'));

            Command::create(array(
	        	'cron' => Input::get('cron'),
	        	'name' => Input::get('command')
	        ));
        } catch (InvalidArgumentException $e) {
        	$error = "Invalid cron.";
        }

        $commands = Command::all();

		return View::make('commands.index', array('commands' => $commands, 'error' => $error));
	}

	/**
	 * Get command is due
	 * @return array
	 */
	public function getCall()
	{
		$results = Command::all();

		$commands = $results->filter(function($result)
		{
		    if($result->is_due)
		    {
		        return $result;
		    }
		});

		return $commands->values()
						->toArray();
	}

	public function getRun()
	{
		// $tmp = new Tmp;
		// $tmp->key = 'Cron Running';
		// $tmp->value = date('Y-m-d H:i:s');
		// $tmp->save();

		$results = Command::all();

		$commands = $results->filter(function($result)
		{
		    if($result->is_due)
		    {
		        return $result;
		    }
		});

		$cronRepo = App::make('CronRepository');
		foreach ($commands as $command)
		{
			call_user_func_array(array($cronRepo, $command->name), array());
		}
	}
}