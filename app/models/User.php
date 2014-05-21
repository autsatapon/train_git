<?php

use Cartalyst\Sentry\Users\Eloquent\User as SentryUser;

class User extends SentryUser {

    protected $memory;

    public function checkPersistCode($params)
    {
        return true;
    }

    public function groups()
    {
        return $this->belongsToMany('Group', 'users_groups', 'user_id', 'group_id');
    }

    public function apps()
    {
        return $this->belongsToMany('PApp', 'user_app', 'user_id', 'app_id');
    }

    /**
     * Check user allow to use app
     * @param  integer  $id App id
     * @return boolean
     */
    public function hasApp($id)
    {
        $app = DB::table('user_app')->whereUserId($this->id)->whereAppId($id)->first();
        return $app ? true : false;
    }

    /**
     * Check user is super admin
     * @return boolean
     */
    public function isSuperAdmin()
    {
        $group = $this->groups()->superAdmin()->first();
        return $group ? true : false;
    }

}