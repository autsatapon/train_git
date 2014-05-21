<?php namespace Wetrust;

class Process {

	protected $config;

    public function __construct(array $config)
    {
		$this->config = $config;

		return $this;
    }

    public function isSuccess()
    {
        return true;
    }

    public function isFails()
    {
        return false;
    }

}