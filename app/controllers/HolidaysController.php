<?php

class HolidaysController extends AdminController {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->theme->breadcrumb()->add('Holidays Management', URL::to('holidays'));
    }
    
    public function getIndex()
    {
        $now = date('Y', strtotime('now'));
        $year = Input::get('year', $now);
        
        $holidays = Holiday::where('started_at', '>=', $year.'-01-01')->where('ended_at', '<=', $year.'-12-31')->orderBy('started_at')->get();
        
        $view = compact('holidays', 'now', 'year');
        
        return $this->theme->of('holidays.index', $view)->render();
    }
    
    public function getCreate()
    {
        $this->theme->breadcrumb()->add('Create', URL::to('holidays/create'));
        
        $this->theme->asset()->container('footer')->usePath()->add('jquery-ui-datetimepicker', 'plugins/timepicker/jquery-ui-timepicker-addon.js', 'jquery');
        $this->theme->asset()->container('footer')->usePath()->add('holidays-create', 'admin/js/holidays-create.js', 'jquery');
        
        return $this->theme->of('holidays.create')->render();
    }
    
    public function postCreate()
    {
        $rules = array(
            'title' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
        );
        
        $validator = Validator::make(Input::only('title', 'started_at', 'ended_at'), $rules);
        
        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator)->withInput();
        }
        else
        {
            $holiday = new Holiday;
            
            $holiday->title = Input::get('title');
            $holiday->description = Input::get('description');
            $holiday->started_at = Input::get('started_at');
            $holiday->ended_at = Input::get('ended_at');
            
            $holiday->save();
        }
        
        return Redirect::to('holidays')->withSuccess('Holiday has been created');
    }
    
    public function getEdit($id)
    {
        try
        {
            $holiday = Holiday::findOrFail($id);
        }
        catch (Exception $e)
        {
            return Redirect::to('/holidays')->withErrors(array('messages' => 'Record not found'));
        }
        
        $view = compact('holiday');
        
        return $this->theme->of('holidays.edit', $view)->render();
    }
    
    public function postEdit($id)
    {
        try
        {
            $holiday = Holiday::findOrFail($id);
        }
        catch (Exception $e)
        {
            return Redirect::to('/holidays')->withErrors(array('messages' => 'Record not found'));
        }
        
        $rules = array(
            'title' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
        );
        
        $validator = Validator::make(Input::only('title', 'started_at', 'ended_at'), $rules);
        
        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator)->withInput();
        }
        else
        {
            $holiday->title = Input::get('title');
            $holiday->description = Input::get('description');
            $holiday->started_at = Input::get('started_at');
            $holiday->ended_at = Input::get('ended_at');
            
            $holiday->save();
        }
        
        return Redirect::to('holidays')->withSuccess(array('messages' => 'Update completed'));
    }
    
    public function getDelete($id)
    {
        try
        {
            $holiday = Holiday::findOrFail($id);
        }
        catch (Exception $e)
        {
            return Redirect::to('/holidays')->withErrors(array('messages' => 'Record not found'));
        }
        
        $holiday->delete();
        
        return Redirect::to('holidays')->withSuccess(array('messages' => 'Delete completed'));
    }

}