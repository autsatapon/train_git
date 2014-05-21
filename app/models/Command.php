<?php

class Command extends Eloquent
{
	protected $table = 'commands';

	protected $fillable = array('cron', 'name');

	protected $appends = array('is_due');

	public function getIsDueAttribute()
	{
		$cron = Cron\CronExpression::factory($this->attributes['cron']);

		return $cron->isDue();
	}
}
