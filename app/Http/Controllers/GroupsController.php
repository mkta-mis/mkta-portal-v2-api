<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\groups;
use App\Models\clientGroup;

class GroupsController extends Controller
{
    public function create_Group($title, $description){
        groups::create(
            array(
                "title"=> $title,
                "description"=> $description
            )
        );
    }
    public function update_Group($id, $arr = array()){
        $curGroup = groups::where('id', '=', $id);
        if( $curGroup->get()->count() == 1 ){
            $curGroup = $curGroup->get()->first();
            foreach ($arr as $key => $value) {
                $curGroup[$key] = $value;
            }
            $curGroup->save();
            return true;
        }else{
            return false;
        }
    }
    public function delete_Group($id){
        groups::where('id', '=', $id)->delete();
        clientGroup::where('group_id', '=', $id)->delete();
        permissionSet::where('reference_ID', '=', $id)->where("type", '=', 2)->delete();
        return true;
    }
}
