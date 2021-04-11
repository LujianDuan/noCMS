<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Search\AdminSearch;
use App\Http\Resources\APi\AdminResource;

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

}
