<?php

class BaseController extends Controller {

	/**
	 * Access control list.
	 *
	 * @var array
	 */
	public static $access = array();

	/**
	 * Controller construct.
	 */
	public function __construct()
	{
		$this->beforeFilter('access.check');

		if (Input::has('migrate'))
		{
			DB::setDefaultConnection('pcms_migrate');
		}
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	/**
	 * Check current environment
	 *
	 * @param  string  $environment Environment
	 * @return boolean
	 */
	public function isEnv($environment)
	{
		$currentEnv = App::environment();
		return (boolean) ($environment == $currentEnv);
	}

	/**
	 * Get current environment
	 *
	 * @return string
	 */
	public function getEnv()
	{
		return App::environment();
	}

}