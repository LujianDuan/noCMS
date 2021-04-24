<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Search\PermissionSearch;
use App\Http\Resources\APi\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function list(Request $request,PermissionSearch $search){
        $params =   $request->input(); 
        $list = $search->search($params);
        $ret = [
            'page'=>$list['page'], 
            'pages'=>$list['pages'], 
            'page_size'=>$list['page_size'], 
            'total'=>$list['total'], 
            'list'=>PermissionResource::collection($list['list']), 
        ];
        return $this->success($ret);
    }

    public function add(Request $request){
        $role = new Permission();
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->module = $request->module;
        $role->description = $request->description;
        $ret = $role->save();
        return $this->success($role);
    }

    public function edit(Request $request){
        $role = Permission::where('id',$request->id)->first();
        if($request->name){
        $role->name = $request->name;
        }
        if($request->display_name){
        $role->display_name = $request->display_name;
        }
        if($request->module){
        $role->module = $request->module;
        }
        if($request->description){
        $role->description = $request->description;
        }
        $ret = $role->save();
        return $this->success($ret);
    }

    public function delete(Request $request){
        $id = $request->id; 
        $ret = Permission::where('id',$id)->delete();
        return $this->success($ret);
    }

}
