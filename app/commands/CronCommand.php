<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:cron';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run Cronjob in PCMS';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// $this->info('Hello World');
		$method = $this->argument('method');

		$cronRepo = new CronRepository;
		$jobStatus = $cronRepo->$method();

		if ($jobStatus == TRUE)
		{
			$this->info('Run Cronjob - Completed.');
		}
		else
		{
			$this->error('Something went wrong.');
		}

		return $jobStatus;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('method', InputArgument::REQUIRED, 'An CronJob method argument.'),
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}