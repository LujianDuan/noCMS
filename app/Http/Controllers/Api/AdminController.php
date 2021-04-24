<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Search\AdminSearch;
use App\Http\Resources\APi\AdminResource;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function list(Request $request,AdminSearch $search){
        $params =   $request->input(); 
        $list = $search->search($params);
        $ret = [
            'page'=>$list['page'], 
            'pages'=>$list['pages'], 
            'total'=>$list['total'], 
            'list'=>AdminResource::collection($list['list']), 
        ];
        return $this->success($ret);
    }

    public function add(Request $request){
        $request->validate([
            'name' => 'required|string|max:255|unique:admins',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|confirmed|min:8',
        ]);
        $user = Admin::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);  
        return $this->success($user);
    }

}
