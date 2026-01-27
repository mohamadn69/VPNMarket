<?php

namespace Modules\MultiServer\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'ms_servers';
    protected $fillable = [
        'location_id', 'name', 'ip_address', 'port', 'username', 'password',
        'is_https', 'path', 'inbound_id', 'capacity', 'current_users', 'is_active'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }


    public function getFullHostAttribute()
    {
        $scheme = $this->is_https ? 'https' : 'http';

        $cleanPath = '/' . ltrim($this->path, '/');
        if($cleanPath === '/') $cleanPath = '';

        return "{$scheme}://{$this->ip_address}:{$this->port}{$cleanPath}";
    }
}
