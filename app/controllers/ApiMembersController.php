<?php

class ApiMembersController extends ApiBaseController {

    /**
     * @api {post} /members/create Create or Update Member Profile
     * @apiName Create Member
     * @apiGroup Member
     *
     * @apiParam {Number} ssoId Member's SSO Id.
     * @apiParam {Number} thai_id Member's Thai Id.
     * @apiParam {Date String} [login_at=now] Last login time.
     * @apiParam {String} email Member's Email or Tel No. (Tel format must be m0xxxxxxx@truelife.com).
     *
     * @apiSuccess {Array} data Member profile.
     */
    public function postCreate($app)
    {
        $data = Input::all();


        if (!isset($data['ssoId']))
        {
            return API::createResponse('Error, ssoId is required.', 400);
        }

        $data = array_merge(array(
            'thai_id' => Input::get('thai_id', null)
            ), $data);

        $loginAt = array_get($data, 'login_at', date('Y-m-d H:i:s'));
        $email = array_get($data, 'email');

        $email_explode = explode('@', $email);
        if (isset($email_explode))
        {
            if ($email_explode[1] == "truelife.com")
            {
                // m0865168051
                if (preg_match("/^m0\d{9}$/", $email_explode[0]))
                {
                    $phone = str_replace("m", "", $email_explode[0]);
                    $email = '';
                }
                else
                {
                    $phone = '';
                }
            }
            else
            {
                $phone = '';
            }
        }
        else
        {
            $phone = '';
        }


        $member = Member::whereAppId($app->id)->whereSsoId($data['ssoId'])->first();

        if (!$member)
        {
            $data = array(
                'app_id' => $app->id,
                'sso_id' => array_get($data, 'ssoId'),
                'thai_id' => array_get($data, 'thai_id'),
                'subscribe' => array_get($data, 'subscribe', 0),
                'login_at' => $loginAt,
                'email' => $email,
                'phone' => $phone,
                'trueyou' => null,
            );

            $member = Member::create($data);
        }
        else
        {
            $member->login_at = $loginAt;
            $member->email = $email;
            $member->phone = $phone;
            $member->save();
        }

        return API::createResponse($member->toArray());
    }

    /**
     * @api {get} /members/profile Get Member Profile
     * @apiName Get Member
     * @apiGroup Member
     *
     * @apiParam {Number} ssoId Member's SSO Id.
     *
     * @apiSuccess {Array} data Member profile.
     */
    public function getProfile($app)
    {
        $ssoId = Input::get('ssoId');

        if ($ssoId == false)
        {
            return API::createResponse('Error, ssoId is required.', 400);
        }

        $member = Member::where('sso_id', $ssoId)->firstOrFail();
        return API::createResponse($member->toArray());
    }

    /**
     * @api {post} /members/activate Activate Member
     * @apiName Activate Member
     * @apiGroup Member
     *
     * @apiParam {Number} ssoId Member's SSO Id.
     *
     * @apiSuccess {String} data Activated.
     */
    public function postActivate($app)
    {
        $ssoId = Input::get('ssoId');

        if ($ssoId == false)
        {
            return API::createResponse('Error, ssoId is required.', 400);
        }

        $member = Member::where('sso_id', $ssoId)->firstOrFail();
        if ($member->activated_at == false)
        {
            $member->activated_at = date('Y-m-d H:i:s');
            $member->save();
        }

        return API::createResponse('Activated', 200);
    }

    /**
     * @api {get} /members/check Check existing Email or Tel no.
     * @apiName Check duplicate Email or Tel no.
     * @apiGroup Member
     *
     * @apiParam {String} email Email to be checked.
     * @apiParam {String} phone Tel no. to be checked.
     *
     * @apiSuccess {String} data This is member.
     */
    public function getCheck($app)
    {
        $data = Input::all();

        $email = !empty($data['email']) ? $data['email'] : '';
        $phone = !empty($data['phone']) ? $data['phone'] : '';

        if ($email != '')
        {
            $member = Member::whereAppId($app->id)->whereEmail($email)->first();
        }

        if (empty($member))
        {
            if (!empty($phone))
            {
                if ($phone != '')
                {
                    $member = Member::whereAppId($app->id)->wherePhone($phone)->first();
                }
            }
        }

        $reponse = array();

        if (empty($email) && empty($phone))
        {
            return API::createResponse('Error! Parameter email or phone are required.', 400);
        }

        if (!empty($member))
        {
            #$reponse = $member->toArray();
            $tmp = $member->toArray();

            return API::createResponse('This is member.', 200);
        }
        else
        {
            return API::createResponse('Not found member.', 404);
        }
    }

    /**
     * @api {post} /members/apply-trueyou Apply Trueyou by Thai Id
     * @apiName Apply Trueyou
     * @apiGroup Member
     *
     * @apiParam {String} customer_ref_id Customer ID (User = sso id, Non-user = random id).
     * @apiParam {String} customer_type (user / non-user).
     * @apiParam {Number} thai_id Thai Id.
     *
     * @apiSuccess {Array} data Member profile.
     */
    public function postApplyTrueyou($app)
    {
        if ( ! Input::has('customer_type') || ! Input::has('customer_ref_id') || ! Input::has('thai_id'))
        {
            return API::createResponse('Error! customer_type, customer_ref_id and thai_id are required.', 400);
        }
        
        $trueCard = App::make('truecard');
        $result = $trueCard->getInfoByThaiId(Input::get('thai_id'))->check();
        
        $member = Member::whereAppId($app->getKey())->whereSsoId(Input::get('customer_ref_id'))->first();
        
        if ($member)
        {
            $member->thai_id = Input::get('thai_id');
            $member->trueyou = $result?:null;
            
            $member->save();
        }
        
        $params = Input::only('customer_type', 'customer_ref_id', 'thai_id');
        
        API::post('cart/apply-trueyou', $params);
        
        return API::createResponse($member, 200);
    }

}
