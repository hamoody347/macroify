<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    function details()
    {
        return $this->hasOne(TenantDetails::class);
    }
    
    // protected $fillable = [
    //     'tenant_details_id'
    // ];

    // public static function getCustomColumns(): array
    // {
    //     return [
    //         'id',
    //         'tenant_details_id',
    //     ];
    // }
}
