<?php

class ApiLastloginController extends ApiBaseController {

	public function getLastlogin($app,$ssoId = 0)
    {

    	$lastLogin = DB::table('members')->where('sso_id', $ssoId)->get();

		return API::createResponse($lastLogin, 200);
	}
}
?>