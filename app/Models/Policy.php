<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    // Columns
    protected $fillable = [
        'name',
        'content',
        'created_by',
        'modified_by',
        'policy_book_id',
    ];

    // Validation
    public static $rules = [
        'name' => 'required|string|max:255',
        'content' => 'required|string',
        'created_by' => 'exists:users,id',
        'modified_by' => 'exists:users,id',
        'policy_book_id' => 'required|exists:policy_books,id',
    ];

    // Relations
    public function policyBook()
    {
        return $this->belongsTo(PolicyBook::class, 'policy_book_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modifiedByUser()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
