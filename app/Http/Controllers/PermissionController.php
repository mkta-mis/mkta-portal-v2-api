<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\permission;
use App\Models\permissionSet;

class PermissionController extends Controller
{
    private $relations = [ 'permission_data' ];
    
    
    #region Roles
    public function get_rolePermission($reference_ID){
        return permissionSet::where("type", '=', 1)
                            ->where("reference_ID", "=", $reference_ID)
                            ->with( $this->relations )
                            ->get();
    }
    public function create_rolePermission(){

    }
    public function delete_rolePermission(){

    }
    #endregion
    #region Group
    public function get_groupPermission($reference_ID){
        return permissionSet::where("type", '=', 2)
                            ->where("reference_ID", "=", $reference_ID)
                            ->with( $this->relations )
                            ->get();
    }
    public function create_groupPermission(){

    }
    public function delete_groupPermission(){

    }
    #endregion
    #region User
    public function get_userPermission($reference_ID){
        return permissionSet::where("type", '=', 3)
                            ->where("reference_ID", "=", $reference_ID)
                            ->with( $this->relations )
                            ->get();
    }
    public function create_userPermission(){

    }
    public function delete_userPermission(){

    }
    #endregion
    #region Product Item Components
    public function get_ProdItemComponents_Permission($reference_ID){
        return permissionSet::where("type", '=', 4)
                            ->where("reference_ID", "=", $reference_ID)
                            ->with( $this->relations )
                            ->get();
    }
    public function create_ProdItemComponents_Permission(){

    }
    public function delete_ProdItemComponents_Permission(){

    }
    #endregion
    

}
