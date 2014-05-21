<?php

use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroup;

class Group extends SentryGroup {

	private static $superAdminId = 1;

	public function users()
    {
        return $this->belongsToMany('User', 'users_groups', 'group_id', 'user_id');
    }

    /**
     * Scope for query superadmin
     * @param  object $query Query builder
     * @return object        Query builder
     */
    public function scopeSuperAdmin($query)
    {
    	return $query->whereId(static::$superAdminId);
    }

    /**
     * Get super admin id
     * @return integer Super admin id
     */
    public static function getSuperAdminId()
    {
    	return static::$superAdminId;
    }

}