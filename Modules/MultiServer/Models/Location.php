<?php

namespace Modules\MultiServer\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'ms_locations';
    protected $fillable = ['name', 'flag', 'slug', 'is_active'];

    public function servers()
    {
        return $this->hasMany(Server::class, 'location_id');
    }
}
