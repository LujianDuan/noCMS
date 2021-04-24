<?php

namespace App\Http\Resources\APi;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id, 
            'name'=>$this->name, 
            'display_name'=>$this->display_name, 
            'module'=>$this->module, 
            'description'=>$this->description, 
            'created_at'=>$this->created_at->format('Y-m-d H:i:s'), 
            'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'), 
        ];
    }
}
