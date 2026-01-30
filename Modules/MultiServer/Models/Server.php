<?php

namespace Modules\MultiServer\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'ms_servers';

    protected $fillable = [
        'location_id',
        'name',
        'ip_address',
        'port',
        'username',
        'password',
        'is_https',
        'path',
        'inbound_id',
        'capacity',
        'current_users',
        'is_active',
        'link_type',
        'subscription_domain',
        'subscription_path',
        'subscription_port',
        'tunnel_address',
        'tunnel_port',
        'tunnel_is_https',
    ];

    protected $casts = [
        'is_https' => 'boolean',
        'is_active' => 'boolean',
        'tunnel_is_https' => 'boolean',
        'port' => 'integer',
        'subscription_port' => 'integer',
        'tunnel_port' => 'integer',
        'capacity' => 'integer',
        'current_users' => 'integer',
        'inbound_id' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * پاکسازی خودکار آدرس IP (حذف http/https و پورت)
     */
    public function setIpAddressAttribute($value)
    {
        $clean = preg_replace('#^https?://#i', '', $value);
        $clean = rtrim($clean, '/');
        $clean = preg_replace('#:\d+$#', '', $clean);
        $this->attributes['ip_address'] = $clean;
    }

    /**
     * ساخت آدرس کامل پنل X-UI برای اتصال API
     */
    public function getFullHostAttribute(): string
    {
        $protocol = $this->is_https ? 'https' : 'http';
        $port = $this->port ?? ($this->is_https ? 443 : 80);
        $path = $this->path ?? '/';

        return "{$protocol}://{$this->ip_address}:{$port}{$path}";
    }
}
