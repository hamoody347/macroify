<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFunction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'status'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function sops()
    {
        return $this->belongsToMany(SOP::class);
    }

    public function faqs()
    {
        return $this->belongsToMany(FAQ::class);
    }

    public function wikis()
    {
        return $this->belongsToMany(Wiki::class);
    }

    public function policyBooks()
    {
        return $this->belongsToMany(PolicyBook::class);
    }
}
