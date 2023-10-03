<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;


    protected $fillable = [
        'question',
        'category_id',
        'department_id',
        'answer',
        'created_by',
        'edited_by',
        'status',
        'general'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function jobFunctions()
    {
        return $this->belongsToMany(JobFunction::class);
    }
}
