<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;


class PolicyBook extends Model
{
    use HasFactory;
    use PivotEventTrait;


    // Columns
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'effective_from_date',
        'general',
        'status'
    ];

    // Validation
    public static $rules = [
        'name' => 'required|string|max:255',
        'description' => 'string|max:255',
        'category_id' => 'required|exists:categories',
        'effective_from_date' => 'required|date',
        'general' => 'boolean',
        'status' => 'required|in:published,draft,unpublished'
    ];

    // Relations
    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function jobFunctions()
    {
        return $this->belongsToMany(JobFunction::class);
    }

    public function policyAssignments()
    {
        return $this->hasMany(PolicyAssignment::class);
    }

    /**
     * This function controls the policy assignments for a specified policy based on it's job functions.
     *
     * @param array<$newJobFunctions>
     */

    // Function to update policy assignments for PolicyBooks
    public function updatePolicyAssignments(array $newJobFunctions)
    {
        // Gets an array of id's newJobFunctions
        // Gets all users that have the removed job functions and deletes the respsective policyAssignment instance for them.
        $currentJobFunctions = $this->jobFunctions()->pluck('job_functions.id')->toArray();

        // Checks the array against the current job functions and establishes which job function id are new and which have been removed
        $addedJobFunctions = array_diff($newJobFunctions, $currentJobFunctions);
        $removedJobFunctions = array_diff($currentJobFunctions, $newJobFunctions);

        // $uniqueUsers = [];

        foreach ($addedJobFunctions as $jobFunctionId) {
            // Gets all users that have the new job functions and creates a policyAssignment instance for them.
            $jobFunction = JobFunction::find($jobFunctionId);
            info('jobfunction');
            info($jobFunction->id);

            $users = $jobFunction->users()->pluck('user_id')->toArray();
            info('users');
            info($users);
            foreach ($users as $user) {
                $policy = PolicyAssignment::where('user_id', $user)->where('policy_book_id', $this->id)->first();

                if (!$policy) {
                    PolicyAssignment::create([
                        'user_id' => $user,
                        'policy_book_id' => $this->id,
                        'assigned_at' => now(),
                        'acknowledged' => false,
                    ]);
                }
            }
        }

        foreach ($removedJobFunctions as $jobFunctionId) {
            // Gets all users that have the new job functions and creates a policyAssignment instance for them.
            $jobFunction = JobFunction::find($jobFunctionId);

            $users = $jobFunction->users()->pluck('user_id')->toArray();

            foreach ($users as $user) {
                $policy = PolicyAssignment::where('user_id', $user)->where('policy_book_id', $this->id)->first();

                if ($policy) {
                    $policy->delete();
                }
            }
        }
    }


    public function markGeneral()
    {
        $users = User::all();

        foreach ($users as $user) {
            $policyAssignment = PolicyAssignment::where([
                'user_id' => $user->id,
                'policy_book_id' => $this->id,
            ])->first();

            // If policy assignment doesn't exist, create it
            if (!$policyAssignment) {
                PolicyAssignment::create([
                    'user_id' => $user->id,
                    'policy_book_id' => $this->id,
                    'assigned_at' => now(),
                    'acknowledged' => false,
                ]);
            }
        }
    }

    public function unMarkGeneral()
    {
        $users = User::all();

        foreach ($users as $user) {
            $policyAssignment = PolicyAssignment::where([
                'user_id' => $user->id,
                'policy_book_id' => $this->id,
            ])->first();

            // If policy assignment doesn't exist, create it
            if ($policyAssignment) {
                $policyAssignment->delete();
            }
        }
    }
}
