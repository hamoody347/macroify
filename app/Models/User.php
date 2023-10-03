<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use PivotEventTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function jobFunctions()
    {
        return $this->belongsToMany(JobFunction::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function policyAssignments()
    {
        return $this->hasMany(PolicyAssignment::class);
    }

    /**
     * This function controls the policy assignments for a specified user based on their job functions.
     *
     * @param array<$newJobFunctions>
     */
    public function updatePolicyAssignments(array $newJobFunctions)
    {
        // Logic to update policy assignments goes here
        $addedJobFunctions = array_diff($newJobFunctions, $this->jobFunctions->pluck('id')->toArray());
        $removedJobFunctions = array_diff($this->jobFunctions->pluck('id')->toArray(), $newJobFunctions);


        // Get unique policy books ids
        $uniquePolicyBookIds = [];
        foreach ($addedJobFunctions as $jobFunctionId) {
            $policyBooks = JobFunction::find($jobFunctionId)->policyBooks()
                ->distinct()
                ->pluck('policy_books.id')
                ->toArray();

            $uniquePolicyBookIds = array_merge($uniquePolicyBookIds, $policyBooks);
        }

        $uniquePolicyBookIds = array_unique($uniquePolicyBookIds);

        // Create new policy assignments for added job functions
        foreach ($uniquePolicyBookIds as $policyBookId) {
            PolicyAssignment::create([
                'user_id' => $this->id,
                'policy_book_id' => $policyBookId, // Determine the policy book ID
                'assigned_at' => now(),
                'acknowledged' => false,
                'acknowledged_at' => null,
            ]);
        }

        // Get the policy book ids for the removed job functions
        $policyBooks = PolicyBook::whereHas('jobFunctions', function ($query) use ($removedJobFunctions) {
            $query->whereIn('job_function_id', $removedJobFunctions);
        })->pluck('id')->toArray();

        // $policyIds = $policyBooks->pluck('policies.*.id')->flatten()->unique()->toArray(); // This is if i want to use policies instead of policy books.

        // Remove policy assignments for removed job functions
        PolicyAssignment::where('user_id', $this->id)
            ->whereIn('policy_book_id', $policyBooks) // Determine the policy book IDs
            ->delete();
    }


    // Handling Changing Job Functions for users
    protected static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($user, $relationName, $jobFunctionIds, $attributes) {
            // Updating the Policies when the employee job functions are altered.
            if ($relationName === 'jobFunctions') {
                $user->updatePolicyAssignments($jobFunctionIds);
            }
        });

        static::pivotDetached(function ($user, $relationName, $jobFunctionIds) {
            // Updating the Policies when the employee job functions are altered.
            if ($relationName === 'jobFunctions') {
                $user->updatePolicyAssignments($jobFunctionIds);
            }
        });
    }
}
