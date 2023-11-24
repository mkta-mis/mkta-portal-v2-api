<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\roles;
use App\Models\userRoles;

use App\Models\permission;
use App\Models\permissionSet;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\misc;
use App\Http\Controllers\ApiTokenController;

use DB;

class ctrl_User extends Controller
{
    #region Users
    public static function user_reset_password($email){
        $password = Str::random(10);
        $curUser = self::user_get('exact-email', array( 'email' => $email ) );
        self::user_update($curUser->id, array( 'password' => Hash::make($password) ));
        return $password;
    }
    public static function user_create( $name, $email, $password, $hasDashboard = 1, $isClient = 0, $isActive = 1 ){
        $emailTaken = User::where('email', '=', $email)->get()->count() > 0;
        if( $emailTaken ){
            return false;
        }
        return User::create(
            array(
                'userToken'     => ApiTokenController::createToken(),
                'name'          => $name,
                'email'         => $email,
                'password'      => Hash::make($password),
                'hasDashboard'  => $hasDashboard,
                'isClient'      => $isClient,
                'isActive'      => $isActive,
            )
        );
    }
    public static function user_update( $id, $data ){
        $curUser = User::where('id', '=', $id);
        if( $curUser->get()->count() != 1 ){
            return false;
        }
        $curUser = $curUser->get()->first();
        foreach ($data as $key => $value) {
            $curUser[$key] = $value;
        }
        $curUser->save();
        return $curUser;
    }
    public static function user_get( $type, $filter = array() ){
        switch (misc::text($type, 'strtolower')) {
            case 'exact-email':
                    $curUser = User::where('email', '=', $filter['email']);
                    if( $curUser->get()->count() != 1 ){
                        return false;
                    }
                    return $curUser->get()->first();
                break;
            case 'exact-token':
                    $curUser = User::where('userToken', '=', $filter['userToken']);
                    if( $curUser->get()->count() != 1 ){
                        return false;
                    }
                    return $curUser->get()->first();
                break;
            case 'exact-token-roles':
                $curUser = self::user_get( 'exact-token', $filter);
                if( $curUser === false ){
                    return false;
                }
                $userRoles = userRoles::where('user_id', '=', $curUser->id)->get()->pluck('role_id');
                $Roles = roles::whereIn('id', $userRoles)->get();
                foreach ($Roles as $key => $value) {
                    $value['permissions'] = permissionSet::where('type', '=', 1)->where('reference_ID', '=', $value->id )->with(['permission_data'])->get();
                }
                return $Roles;
                break;
            case 'exact':
                    $curUser = User::where('isClient', '=', 0)->where('id', '=', $filter['id']);
                    if( $curUser->get()->count() != 1 ){
                        return false;
                    }
                    return $curUser->get()->first();
                break;
            case 'search':
                return User::where('isClient', '=', 0)->where($filter)->get();
                break;
            default:
                return User::where('isClient', '=', 0)->get();
                break;
        }
    }
    public static function user_get_roles_information($user_id){
        
    }
    public static function user_set_role( $user_id, $role_id ){
        userRoles::where('user_id', '=', $user_id)->delete();
        userRoles::create(
            array(
                'role_id' => $role_id,
                'user_id' => $user_id,
            )
        );
        return true;
    }
    public static function user_append_role( $user_id, $role_id ){
        $isRegistered = userRoles::where('user_id', '=', $user_id)->where('role_id', '=', $role_id);
        if( $isRegistered->get()->count() == 0 ){
            userRoles::create(
                array(
                    'role_id' => $role_id,
                    'user_id' => $user_id,
                )
            );
        }
        return true;
    }
    public static function user_remove_role( $user_id, $role_id ){
        userRoles::where('user_id', '=', $user_id)->where('role_id', '=', $role_id)->delete();
        return true;
    }
    #endregion
    #region Roles
    public static function role_permission_append($key, $role_id){
        $curPermission = permissionSet::where('reference_ID', '=', $role_id)->where('key', '=', $key)->where('type', '=', 1);;
        if( $curPermission->get()->count() == 0 ){
            permissionSet::create(
                                array(
                                        'reference_ID'=> $role_id,
                                        'key'=> $key,
                                        'type'=> 1,
                                )
                            );
        }
        return true;

    }
    public static function role_permission_remove($key, $role_id){
        permissionSet::where('reference_ID', '=', $role_id)->where('key', '=', $key)->where('type', '=', 1)->delete();
        return true;
    }
    public static function role_get($type, $filter = array()){
        switch (misc::text($type, 'strtolower')) {
            case 'exact-index':
                    $curRole = roles::where('id', '=', $filter['id']);
                    if( $curRole->get()->count() != 1 ){
                        return false;
                    }
                    return $curRole->get()->first();
                break;
            case 'search':
                return roles::where($filter)->get();
                break;
            case 'exact-index-permissions':
                return permission::whereIn( 
                                            'key', 
                                            permissionSet::where( 'type', '=' , 1 )->where( 'reference_ID' , '=' , $filter['id'] )->get()->pluck('key')
                                        )
                                 ->get();
                break;
            default:
                return roles::get();
                break;
        }
    }
    public static function role_create( $title, $description ){
        $title = misc::text($title);
        $description = misc::text($description);
        $emailTaken = roles::where('title', '=', $title)->get()->count() > 0;
        if( $emailTaken ){
            return false;
        }
        roles::create(array(
            'title' => $title,
            'description' => $description
        ));
    }
    public static function role_update($id, $title, $description){
        $title = misc::text($title);
        $description = misc::text($description);
        $curRole = roles::where('id', '=', $id);

        if( $curRole->get()->count() == 0 ){
            return false;
        }
        $curRole = $curRole->get()->first();
        $curRole->title = $title;
        $curRole->description = $description;
        $curRole->save();
    }
    #endregion
    #region Permission
    public static function permission_get($type, $filter=array()){
        switch (misc::text($type, 'strtolower')) {
            case 'exact-index':
                    $curPermission = permission::where('id', '=', $filter['id']);
                    if( $curPermission->get()->count() != 1 ){
                        return false;
                    }
                    return $curPermission->get()->first();
                break;
            case 'search':
                return permission::where($filter)->get();
                break;
            default:
                return permission::get();
                break;
        }
    }
    public static function permission_create($key, $title, $description, $isDefault = 0){
        $keyTaken = permission::where('key', '=', $key)->get()->count() > 0;
        if( $keyTaken ){
            return false;
        }
        return permission::create(
            array(
                "key"=>$key,
                "title"=>$title,
                "description"=>$description,
                "isDefault"=>$isDefault
            )
        );
    }
    public static function permission_update($id, $arr = array()){
        $curPermission = permission::where('id', '=', $id);
        if( $curPermission->get()->count() == 1 ){
            $curPermission = $curPermission->get()->first();
            foreach ($arr as $key => $value) {
                $curPermission[$key] = $value;
            }
            $curPermission->save();
            return true;
        }else{
            return false;
        }
    }
    public static function permission_delete($key){
        permission::where('key', '=', $key)->delete();
        permissionSet::where('key', '=', $key)->delete();
        return true;
    }
    #endregion
}
