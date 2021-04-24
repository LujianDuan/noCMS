<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Search\RoleSearch;
use App\Http\Resources\APi\RoleResource;
use App\Models\Role;

class RoleController extends Controller
{
    public function list(Request $request,RoleSearch $search){
        $params =   $request->input(); 
        $list = $search->search($params);
        $ret = [
            'page'=>$list['page'], 
            'pages'=>$list['pages'], 
            'page_size'=>$list['page_size'], 
            'total'=>$list['total'], 
            'list'=>RoleResource::collection($list['list']), 
        ];
        return $this->success($ret);
    }

    public function add(Request $request){
        $role = new Role();
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $ret = $role->save();
        return $this->success($role);
    }

    public function edit(Request $request){
        $role = Role::where('id',$request->id)->first();
        if($request->name){
        $role->name = $request->name;
        }
        if($request->display_name){
        $role->display_name = $request->display_name;
        }
        if($request->description){
        $role->description = $request->description;
        }
        $ret = $role->save();
        return $this->success($ret);
    }

    public function delete(Request $request){
        $id = $request->id; 
        $ret = Role::where('id',$id)->delete();
        return $this->success($ret);
    }

}
