<?php

class AdminController extends BaseController {

    protected $theme, $data, $user;

    public function __construct()
    {
        parent::__construct();

        if (get_class($this) == 'AuthController')
        {
            $this->theme = Theme::uses('admin')->layout('admin-auth');
        }
        else
        {
            $this->theme = Theme::uses('admin')->layout('admin-dashboard');
        }

        $this->theme->breadcrumb()->add('PCMS', URL::to('/'));

        $this->data = array();

        // $this->user = Sentry::getUser();

        if(Input::get('live', null) !== null)
        {
            $this->theme->asset()->writeContent('meta-live', '<meta http-equiv="refresh" content="3" />');
        }
    }
}