<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status'
    ];

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

    public function policies()
    {
        return $this->hadMany(Policy::class);
    }
}
