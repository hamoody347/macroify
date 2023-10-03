<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    public function jobFunctions()
    {
        return $this->hasMany(JobFunction::class);
    }

    public function sops()
    {
        return $this->hasMany(SOP::class);
    }

    public function wikis()
    {
        return $this->hasMany(Wiki::class);
    }

    public function faqs()
    {
        return $this->hasMany(FAQ::class);
    }
}
