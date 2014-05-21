<?php
/**
 *  @author :  Preme W. <preme_won@truecorp.co.th>
 *  @since  :  Jan 16, 2014
 *  @version :  1.0
 *  @package   :  PCMS 
 *
 */
class BannerPosition extends PCMSModel { 

    public $softDelete = false; 
    
    /*
    *  Can use in laravel 4.1 can't use in v. 4.0
    public function banners()
    {
        return $this->hasManyThrough('Banner', 'BannerGroup', 'banner_group_id', 'banner_position_id');
    }
    */

    //--- Method name must be used Camel Case. *** do not use underscore *** ---//
    public function bannerGroups()
    {
        return $this->hasMany('BannerGroup');
    }
}