<?php

class GroupsController extends AdminController
{

    public static $access = array(
        'getIndex' => 'role.read',
        'getCreate' => 'role.write',
        'postCreate' => 'role.write',
        'getEdit' => 'role.write',
        'postEdit' => 'role.write'
        );

    public function __construct()
    {
        parent::__construct();
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'User role',
                'url'   => URL::action('GroupsController@getIndex')
            )
        ));
    }

    private function checkGroup($id)
    {
        try {
            $group = Sentry::getGroupProvider()->findById($id);
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            return App::abort(404);
        }
        return $group;
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
                        { "bSortable": false, "aTargets": [ -1 ] },
                        { "bSearchable": false, "aTargets": [ -1 ] }
                    ],
                    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                        $(\'td\', nRow).attr(\'nowrap\',\'nowrap\');
                        $(\'td:not(:eq(1))\', nRow).addClass(\'center\');
                        return nRow;
                    },
                    "fnDrawCallback": function (oSettings) {
                        //self.popover();
                    }
                });
            });
            ', array('datatable'));

        $groups = Group::join('users_groups', 'groups.id','=','users_groups.group_id', 'left')
            ->join('users', 'users.id','=','users_groups.user_id', 'left')
            ->select('groups.name as name', DB::raw('COUNT(users.id) as users_count'), 'groups.permissions', 'groups.id')
            ->groupBy('groups.name')->get();


        return $this->theme->of('groups.index', array(
                'error' => Session::get('error'),
                'success' => Session::get('success'),
                'groups' => $groups
            ))->render();
    }
/*
    public function postIndex()
    {
        $groups = Group::join('users_groups', 'groups.id','=','users_groups.group_id', 'left')
            ->join('users', 'users.id','=','users_groups.user_id', 'left')
            ->select('groups.name as name', DB::raw('COUNT(users.id) as users_count'), 'groups.permissions', 'groups.id')
            ->groupBy('groups.name');

        return Datatables::of($groups)

            ->edit_column('permissions', '
                <?php
                    ksort($permissions);
                    foreach($permissions as $perm => $v)
                    {
                        list($controller, $action) = explode(".", $perm);
                        $pretty_action = str_replace("-"," ",$action);
                        echo "User can \"{$pretty_action}\" {$controller}.<br>";
                    }
                ?>')

            ->add_column('operations','
                {{ HTML::buttonLink(\'Edit\', URL::action( \'GroupsController@getEdit\').\'/\'.$id, \'edit\', \'\', array(\'class\' => \'btn-small\')) }}
            ')
            ->remove_column('id')
            ->make();
    }
*/
    public function getCreate()
    {
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'Create',
                'url'   => URL::action('GroupsController@getCreate')
            )
        ));
        $group = null;
        $permissions = array();
        $rules = Config::get('rules');
        $view = compact('group', 'permissions', 'rules');
        return $this->theme->of('groups.form', $view)->render();
    }

    public function postCreate()
    {
        $rules = $this->validatorGroup();

        $validator = Validator::make(Input::all(), $rules);

        if ($validator -> fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        //Register group
        try {
            // Create the group
            $group = Sentry::getGroupProvider()->create(array(
                'name'        =>  Input::get('name')
            ));

            // Put permissions from Input to variable for update db.
            $configRules = Config::get('rules');
            $permissions_data = array();
            foreach ($configRules as $controller => $perms) {
                foreach ($perms as $perm) {
                    $input = $controller.'_'.$perm;
                    $rule = $controller.'.'.$perm;
                    $permissions_data[$rule] = Input::get($input);
                }
            }

            //Update permission
            $group->permissions = $permissions_data;
            $group->save();
        } catch (Cartalyst\Sentry\Groups\GroupExistsException $e) {
            return Redirect::action('GroupsController@getIndex')->with('error', 'Group already exists.');
        }

        return Redirect::action('GroupsController@getIndex')
            ->with('success', 'Group created.');
    }

    public function getEdit($id)
    {
        $this->theme->breadcrumb()->add(array(
            array(
                'label' => 'Edit',
                'url'   => URL::action('GroupsController@getEdit').'/'.$id
            )
        ));
        $group = $this->checkGroup($id);

        $permissions = $group->getPermissions();
        $rules = Config::get('rules');
        $view = compact('group', 'permissions', 'rules');

        return $this->theme->of('groups.form', $view)->render();
    }

    public function postEdit($id)
    {
        $rules = $this->validatorGroup();

        $validator = Validator::make(Input::all(), $rules);

        if ($validator -> fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $group = Sentry::getGroupProvider()->findById($id);

            $group->name = Input::get('name');

            // Put permissions from Input to variable for update db.
            $configRules = Config::get('rules');
            $permissions_data = array();
            foreach ($configRules as $controller => $perms) {
                foreach ($perms as $perm) {
                    $input = $controller.'_'.$perm;
                    $rule = $controller.'.'.$perm;
                    $permissions_data[$rule] = Input::get($input);
                }
            }

            //Update permission
            $group->permissions = $permissions_data;
            $group->save();
        } catch (Cartalyst\Sentry\Groups\GroupExistsException $e) {
            return Redirect::action('GroupsController@getIndex')->with('error', 'Group already exists.');
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            return App::abort(404);
        }

        // Group information was updated
        return Redirect::action('GroupsController@getIndex')
            ->with('success', 'Group updated.');
    }

    // public function getDelete($id)
    // {
    //     $group = $this->checkGroup($id);
    //     $group->delete();

    //     return Redirect::action('GroupsController@getIndex')->with('success', __('Group deleted.'));
    // }

    private function validatorGroup()
    {
        $rules = array(
            'name' => 'required'
        );

        //Loop rules config
        $configRules = Config::get('rules');
        foreach ($configRules as $controller => $perms) {
            foreach ($perms as $perm) {
                //Get rules config and put to validator
                $index = $controller.'_'.$perm;
                $rules[$index] = 'required|in:0,1';
            }
        }
        return $rules;
    }

}
