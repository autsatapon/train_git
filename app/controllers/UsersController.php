<?php

class UsersController extends AdminController {

    public static $access = array(
        'getIndex' => 'user.read',
        'getCreate' => 'user.write',
        'postCreate' => 'user.write',
        'getEdit' => 'user.write',
        'postEdit' => 'user.write',
        'getPerms' => 'user.write',
        'postPerms' => 'user.write'
        );

    private $userIsSuperAdmin = null;

    public function __construct()
    {
        parent::__construct();
        $this->userIsSuperAdmin = Sentry::getUser()->isSuperAdmin();
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'User',
                'url'   => URL::action('UsersController@getIndex')
            )
        ));
    }

    private function checkUser($id)
    {
        try {
            $user = Sentry::getUserProvider()->findById($id);
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return App::abort(404);
        }

        if( ! $this->userIsSuperAdmin)
        {
            if($user->isSuperAdmin())
            {
                App::abort(404);
            }
        }

        return $user;
    }

    public function getIndex()
    {
        $this->theme->asset()->container('footer')->usePath()->add('datatable', 'plugins/datatables/jquery.dataTables.min.js', array('jquery'));
        $this->theme->asset()->container('footer')->writeScript('datatable-code', '
            $(function() {
                $(\'#datatables_admin_default\').dataTable( {
                    //"bServerSide": true,
                    //"sServerMethod": "POST",
                    //"sAjaxSource": location.href,
                    "aaSorting": [[ 0, "desc" ]],
                    "sPaginationType": "full_numbers",
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ -1, 3 ] },
                        { "bSearchable": false, "aTargets": [ -1, 3 ] }
                    ],
                    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                        $(\'td\', nRow).attr(\'nowrap\',\'nowrap\');
                        $(\'td:not(:eq(1))\', nRow).addClass(\'table-center\');
                        return nRow;
                    },
                    "fnDrawCallback": function (oSettings) {
                        //self.popover();
                    }
                });
            });
            ', array('datatable'));

        $users = User::join('users_groups', 'users.id','=','users_groups.user_id', 'left')
            ->join('groups', 'groups.id','=','users_groups.group_id', 'left')
            ->join('user_app', 'users.id','=','user_app.user_id', 'left')
            ->join('apps', 'apps.id','=','user_app.app_id', 'left')
            ->select('users.display_name', 'users.email', 'groups.name as group_name', DB::raw('GROUP_CONCAT(apps.name SEPARATOR \'<br>\') as app_name'), 'users.last_login', 'users.id as id')
            ->groupBy('users.id')->get();

        return $this->theme->of('users.index', array(
                'error' => Session::get('error'),
                'success' => Session::get('success'),
                'users' => $users
            ))->render();
    }

    // public function postIndex()
    // {

    //     $users = User::join('users_groups', 'users.id','=','users_groups.user_id', 'left')
    //         ->join('groups', 'groups.id','=','users_groups.group_id', 'left')
    //         ->join('user_app', 'users.id','=','user_app.user_id', 'left')
    //         ->join('apps', 'apps.id','=','user_app.app_id', 'left')
    //         ->select('users.display_name', 'users.email', 'groups.name as group_name', DB::raw('GROUP_CONCAT(apps.name SEPARATOR \'<br>\') as app_name'), 'users.last_login', 'users.id as id')
    //         ->groupBy('users.id');

    //     if( ! $this->userIsSuperAdmin)
    //     {
    //         $users = $users->where('groups.id', '<>', '1');
    //     }

    //     return Datatables::of($users)
    //         ->add_column('operations','
    //             {{ HTML::buttonLink(\'Edit\', URL::action( \'UsersController@getEdit\')."/".$id, \'edit\', \'\', array(\'class\' => \'btn-small\')) }}
    //             {{ HTML::buttonLink(\'Edit role\', URL::action( \'UsersController@getPerms\')."/".$id, \'open\', \'\', array(\'class\' => \'btn-small\')) }}
    //         ')
    //         ->remove_column('id', 'permissions', 'activated', 'login', 'name')
    //         ->make();
    // }

    public function getCreate()
    {
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'Create',
                'url'   => URL::action('UsersController@getCreate')
            )
        ));
        $apps = PApp::all();

        $groups = Group::all();
        $user = null;

        $view = compact('groups', 'apps', 'user');
        return $this->theme->of('users.form', $view)->render();
    }

    public function postCreate()
    {
        $rules = $this->validatorUser();

        $group = Input::get('group');

        $validator = Validator::make(Input::all(), $rules);

        if ($validator -> fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            //Register user
            $user = Sentry::register(array(
                'email'        => Input::get('email'),
                'password'     => Input::get('password'),
                'display_name' => Input::get('display_name')
            ));

            //Activate user
            $activationCode = $user->getActivationCode();
            $user->attemptActivation($activationCode);

            // Find the group using the group id
            $adminGroup = Sentry::findGroupById($group);
            $user->addGroup($adminGroup);

            //Update app's user
            $app = array();
            foreach (Input::all() as $field => $input)
            {
                if (preg_match('#apps_([0-9]+)#', $field, $matches))
                {
                    $app[] = $matches[1];
                }
            }
            $user->apps()->sync($app);

        } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
            return Redirect::action('UsersController@getIndex')->with('error', 'User with this login already exists.');
        }

        return Redirect::action('UsersController@getIndex')->with('success', 'User created.');
    }

    public function getEdit($id)
    {
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'Edit',
                'url'   => URL::action('UsersController@getEdit').'/'.$id
            )
        ));
        $user = $this->checkUser($id);
        $apps = PApp::all();

        return $this->theme->of('users.form', array('user' => $user, 'apps' => $apps))->render();
    }

    public function postEdit($id)
    {
        $user = $this->checkUser($id);

        $rules = $this->validatorUser("edit");

        $validator = Validator::make(Input::all(), $rules);

        if ($validator -> fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            //Update user data
            $user->email = Input::get('email');
            $user->display_name = Input::get('display_name');
            if (Input::get('password2')) {
                $user->password = Input::get('password2');
            }

            //Update app's user
            $app = array();
            foreach (Input::all() as $field => $input) {
                if (preg_match('#apps_([0-9]+)#', $field, $matches)) {
                    $app[] = $matches[1];
                }
            }
            $user->apps()->sync($app);
            $user->save();

        } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
            return Redirect::action('UsersController@getIndex')->with('error', 'User with this login already exists.');
        }

        //User updated
        return Redirect::action('UsersController@getIndex')
            ->with('success', 'User updated.');
    }

    private function validatorUser($action = "create") {
        $rules = array(
            'email' => 'required|email',
            'display_name' => 'required',
            'password2' => 'required_with:password|same:password'
        );

        if($action == "create")
        {
            $rules['password'] = 'required|min:5';
        }

        $apps = PApp::all();
        foreach ($apps as $app) {
            $rules['app_'.$app->id] = 'in:1';
        }

        return $rules;
    }

    // public function getDelete($id)
    // {
    //     $user = $this->checkUser($id);
    //     $user->delete();

    //     return Redirect::action('UsersController@getIndex')->with('success', 'User deleted.');
    // }

    public function getPerms($id)
    {
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'Edit role',
                'url'   => URL::action('UsersController@getPerms').'/'.$id
            )
        ));

        $user = $this->checkUser($id);

        $permissions = $user->getPermissions();
        $rules = Config::get('rules');

        if( ! $this->userIsSuperAdmin)
        {
            $rules = null;
        }

        $allGroups = Group::all();

        if( ! $this->userIsSuperAdmin)
        {
            $allGroups = $allGroups->filter(function($item) {
                if( $item->id != Group::getSuperAdminId() )
                {
                    return $item;
                }
            });
        }


        return $this->theme->of('users.permissions', array('user' => $user, 'rules' => $rules, 'permissions' => $permissions, 'groups' => $allGroups))->render();
    }

    public function postPerms($id)
    {
        $user = $this->checkUser($id);

        $rules = array(
            'group' => 'required|integer'
        );

        //Loop rules config
        $configRules = Config::get('rules');
        foreach ($configRules as $controller => $perms) {
            foreach ($perms as $perm) {
                //Get rules config and put to validator
                $index = $controller.'_'.$perm;
                $rules[$index] = 'required|in:0,1,-1';
            }
        }

        $messages = array(
            'group.integer' => 'Must select group that user should belong to.',
        );


        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator -> fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        if( ! $this->userIsSuperAdmin)
        {
            if(Input::get('group') == Group::getSuperAdminId())
            {
                return Redirect::action('UsersController@getIndex')->with('error', 'You can\'t assign user to SuperAdmin group.');
            }
        }


        // assign group to user
        $group = Sentry::getGroupProvider()->findById(Input::get('group'));
        if($group)
        {
            $user->groups()->sync(array(Input::get('group')));
        }

        if($this->userIsSuperAdmin)
        {

            // Put permissions from Input to variable for update db.
            $permissions_data = array();
            foreach ($configRules as $controller => $perms) {
                foreach ($perms as $perm) {
                    $input = $controller.'_'.$perm;
                    $rule = $controller.'.'.$perm;
                    $permissions_data[$rule] = Input::get($input);
                }
            }

            //Update permission
            $user->permissions = $permissions_data;
        }

        $user->save();

        //User updated
        return Redirect::action('UsersController@getIndex')
            ->with('success', 'User\'s permissions updated.');
    }

}
