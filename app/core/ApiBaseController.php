<?php

class ApiBaseController extends BaseController {

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return mixed
     */
    public function missingMethod($parameters = array())
    {
        return API::createResponse(array('message' => 'Method unavailable.'), 404);
    }

}