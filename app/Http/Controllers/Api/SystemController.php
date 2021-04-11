<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{

    /**
     *
     * 返回路由让前端使用
     */
    public function routes(){
        return $this->success([]);
    }
}
