<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Models\files;

class ctrl_Files extends Controller
{
    
    public static function files_upload(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        if( !$req->has('image') ){
            return array(
                "code" => 202,
                "message" => "File not included in the submitted form."
            );
        }
        if( !$req->hasFile('image') ){
            return array(
                "code" => 202,
                "message" => "File not included in the submitted form."
            );
        }

        $curFile = $req->file('image');
        $fileName =  $req->file('image')->getClientOriginalName();
        $ext = $req->file('image')->getClientOriginalExtension();
        $type = $req->file('image')->getClientMimeType();

        $file_name = $req->file('image')->getClientOriginalName();
        $generated_new_name = bin2hex($fileName).'.'.$ext;
        $res['message'] = 'File upload success';

        $curFile = self::file_create(
            $fileName, 
            $fileName.".".$ext, 
            $type, 
            $tokenData['reference_ID']
        );
        $generated_new_name = bin2hex($curFile->id).".".$ext;
        
        $data = Storage::disk('s3')->put( "", $req->file('image') );
        $curFile = self::file_update($curFile->id, 
                                                array(
                                                    "filename"=>$data
                                                )
                                            );
        return array(
            "code" => 200, 
            "message" => "Upload Success",
            "data" => $data
        );
        
    }
    public static function files_search(Request $req){
        $files = array();

        $files  = files::where('title', 'LIKE', '%'.$req->title.'%');

        if( $req->has('type') ){
            $files = $files->where('type', 'LIKE', '%'.$req->type.'%');
        }

        $files = $files->orderBy('id', 'desc')->get();
        return array(
            "code"      => 200,
            "results"   => $files
        );
    }
    public static function file_create($title, $filename, $type, $user_id){
        $curFile = files::create(
            array(
                'title'         =>  $title,
                'filename'      =>  $filename,
                'type'          =>  $type,
                'uploader_id'   =>  $user_id
            )
        );
        return $curFile;
    }
    public static function file_update($id, $data){
        $curFile = files::where('id', '=', $id);
        if( $curFile->get()->count() != 1 ){
            return false;
        }
        $curFile = $curFile->get()->first();
        foreach ($data as $key => $value) {
            $curFile[$key] = $value;
        }
        $curFile->save();
        return $curFile;
    }
    public static function file_delete($id){
        $curFile = files::where('id', '=', $id);
        if( $curFile->get()->count() != 1 ){
            return false;
        }
        $curFile = $curFile->get()->first();

        if(file_exists(public_path('storage/'.$curFile->filename))){
            unlink(public_path('storage/'.$curFile->filename));
        }else{
            $curFile->delete();
            return false;
        }
        
        $curFile->delete();
        return true;
    }
}
