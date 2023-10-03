<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyAssignment extends Model
{
    use HasFactory;

    // Columns
    protected $fillable = [
        'user_id',
        'policy_book_id',
        'assigned_at',
        'acknowledged',
        'acknowledged_at'
    ];

    // Validation
    public static $rules = [
        'user_id' => 'required|exists:users,id',
        'policy_book_id' => 'required|exists:policy_books,id',
        'assigned_at' => 'required|date',
        'acknowledged' => 'boolean',
        'acknowledged_at' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function policyBook()
    {
        return $this->belongsTo(PolicyBook::class);
    }
}
