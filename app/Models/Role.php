<?php namespace App\Models;

use Trebol\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
];


}
