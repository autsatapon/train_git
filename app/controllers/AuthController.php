<?php

class AuthController extends AdminController {

    public function __construct()
    {
        parent::__construct();
        $this->theme = Theme::uses('admin')->layout('admin-auth');
    }


    public function getIndex()
    {
        return Redirect::action('AuthController@getLogin');
    }


    public function getLogin()
    {
        $success = Session::get('success');
        $error   = Session::get('error');
        $view    = compact('success', 'error');
        return $this->theme->of('auth.login', $view)->render();
    }

    public function postLogin()
    {
        $error = '';

        $rules = array(
            'email'    => 'required|email',
            'password' => 'required'
        );

        // $validator = Validator::make(Input::all(), $rules);

        $validator = Validator::make(array_map('trim',Input::all()),
                                array(
                                    'password' => 'required',
                                    'email'    => 'required|email')
                                );

        if ($validator->fails())
        {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        try
        {
            // Set login credentials
            $credentials = array(
                'email'    => Input::get('email'),
                'password' => Input::get('password'),
            );

            // Try to authenticate the user
            $user = Sentry::authenticate($credentials, (boolean) Input::get('remember'));
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $error = 'Email field is required.';
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            $error = 'Password field is required.';
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $error = 'User or password is incorrect.';
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $error = 'User is not activated.';
        }
        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
        {
            $error = 'User is suspended.';
        }
        catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
        {
            $error = 'User is banned.';
        }

        if ( ! empty($error))
        {
            //Logout user that wrong group or deleted
            Sentry::logout();

            //Catch error and throw back to login page
            return Redirect::back()->with('error', $error)->withInput();
        } else {
            return Redirect::to('/#logged_in');
        }
    }

    public function getLogout()
    {
        Sentry::logout();

        return Redirect::to('/#logged_out');
    }

    /**
     * Reset password page
     */
    public function getResetpw()
    {
        $error = Session::get('error');
        $view = compact('error');
        return $this->theme->of('auth.resetpw', $view)->render();
    }

    public function postResetpw()
    {
        $rules = array(
            'email' => 'required|email'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator -> fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = Sentry::getUserProvider()->findByLogin(Input::get('email'));
            $resetCode = $user->getResetPasswordCode();
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::back()->with('error', 'User was not found.');
        }

        $urlReset = URL::action('AuthController@getResetpw2', array('id' => $user->id, 'code' => $resetCode) );

        if ($this->isEnv('production')) {
            //Send the email
            Mail::send('emails.auth.resetpw', array('url' => $urlReset), function($m) use ($user) {
                $name = trim($user->first_name.' '.$user->last_name);
                $m->to($user->email, $name)->subject('Password Reset');
            });
        }

        $testLink = '';

        if(! $this->isEnv('production')) {
            $testLink = 'Test link: <a href="'.$urlReset.'">Link for reset password</a>';
        }

        // redirect on success
        return Redirect::to('auth/login')
            ->with('success', 'We sent link for reset password to your email. Please check your email for further instructions.' .$testLink);
    }


    /**
     * Reset password page - Link from email
     */
    public function getResetpw2()
    {
        $error = null;
        try {
            // Find the user using the user id
            $user = Sentry::getUserProvider()->findById(Input::get('id', null));

            // Check if the reset password code is valid
            if (! $user->checkResetPasswordCode(Input::get('code', null))) {
                $error = 'You don\'t have access to reset password page.';
            }
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            $error = 'User was not found.';
        }

        if($error) {
            return Redirect::action('AuthController@getLogin')->with('error', $error);
        }

        $error = Session::get('error');
        $view = compact('error', 'user');

        //Show form
        return $this->theme->of('auth.resetpw2', $view)->render();
    }


    public function postResetpw2()
    {
        $error = null;
        try {
            // Find the user using the user id
            $user = Sentry::getUserProvider()->findById(Input::get('id', null));

            // Check if the reset password code is valid
            if (! $user->checkResetPasswordCode(Input::get('code', null))) {
                $error = 'You don\'t have access to reset password page.';
            }
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            $error = 'User was not found.';
        }

        if($error) {
            return Redirect::action('AuthController@getLogin')->with('error', $error);
        }

        //Validate form
        $rules = array(
            'password' => 'required|confirmed|min:5'
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator -> fails()) {
            return Redirect::back()
                            ->withErrors($validator)
                            ->withInput();
        }

        if ($user->attemptResetPassword(Input::get('code'), Input::get('password'))) {
            return Redirect::action('AuthController@getLogin')->with('success', 'Your password reseted. Please login with new password.');
        } else {
            return Redirect::action('AuthController@getLogin')->with('error', 'Reset password failed. Please try again.');
        }
    }

    public function getSignup()
    {
        $this->theme->setTitle('Admin Signup');

        return $this->theme->of('auth.signup', $this->data)->render();
    }

    public function postSignup()
    {
        $postData = array(
            'display_name'    => Input::get('display_name'),
            'email'       => Input::get('email'),
            'password'    => Input::get('password'),
            'password_confirmation'    => Input::get('password_confirmation'),
            'activated'   => 1,
        );

        $user = $this->trySignup($postData);


        // $user_data = Input::only('username', 'password', 'password_confirmation', 'email');

        // if ( User::validate($user_data) )
        // {
        //     $user_data['password'] = Hash::make($user_data['password']);
        //     $user = User::create($user_data);

        //     $this->data['signup_success'] =  TRUE;
        // }
        // else
        // {
        //     $this->data['validate_errors'] =  User::errors();
        // }

        // return $this->getSignup();

        return $this->getSignup();
    }


    private function trySignup( $userData )
    {
        $user = null;

        // Please Validate $userData before Create the user.
        $rules = array(
            'email' =>  'required|email|unique:users',
            'display_name' => 'required',
            'password' => 'required|confirmed|min:6'
        );

        $v = Validator::make($userData, $rules);
        if ($v->fails())
        {
            $this->data['validate_errors'] = $v->messages();
            return;
        }

        try
        {
            unset($userData['password_confirmation']);
            // Create the user
            $user = Sentry::getUserProvider()->create($userData);


            // Find the group using the group id
            // $adminGroup = Sentry::getGroupProvider()->findById(1);

            // Assign the group to the user
            // $user->addGroup($adminGroup);


            $this->data['signup_success'] = TRUE;
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            // $this->data['sentry_errors'] = 'Login field is required.';
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            // $this->data['sentry_errors'] = 'Password field is required.';
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            // $this->data['sentry_errors'] = 'User with this login already exists.';
        }
        catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
        {
            // $this->data['sentry_errors'] = 'Group was not found.';
        }

        return $user;
    }

}
