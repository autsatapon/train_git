<?php

class HolidayRepository implements HolidayRepositoryInterface {
    
    public function getFromNow()
    {
        $now = date('Y-m-d');
        $holidays = Holiday::where('started_at', '>=', $now)->get();
        
        $days = array();
        
        $holidays->each(function($day) use (&$days)
        {
            $start = $day->started_at;
            $end = $day->ended_at;
            
            while ($start <= $end)
            {
                $days[] = $start;
                
                $start = date('Y-m-d', strtotime('+ 1 day', strtotime($start)));
            }
        });
        
        return $days;
    }

}
