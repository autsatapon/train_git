<?php

class Campaign extends PCMSModel {

    protected $fillable = array('name', 'detail', 'note');

    public static $labels = array(
        'name' => 'Campaign Name',
		 'budget' => 'Budget',
		 'used_budget' => 'Used Budget',
		 'used_times' => 'Used Times',
		 'used_users' => 'Used Users',
		 'gifted_items' => 'Gifted Items',
		 'period' => 'Period',
		 'status' => 'Status',
		'action' => 'Action',
    );

    public static $rules = array(
        'name' => 'required',
        'status' => 'required'
    );

    public function note()
    {
        return $this->morphOne('Note', 'noteable');
    }

	public function promotions()
    {
        return $this->hasMany('Promotion');
    }

    public function scopeActive($query)
    {
        $currentDateTime = date('Y-m-d H:i:s');

        return $query->where('end_date', '>=', $currentDateTime)->where('start_date', '<=', $currentDateTime)->where('status', 'activate');
    }

    public function checkActive()
	{
		$start_date = strtotime($this->start_date);
		$end_date = strtotime($this->end_date);
		$current_date = time();

		$isActivate = ($this->status == 'activate') ? TRUE : FALSE ;

        return (boolean) ($end_date > $current_date && $current_date > $start_date && $isActivate);
	}


}