<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Policy;
use App\Models\Category;
use App\Models\PolicyBook;
use App\Models\Department;
use App\Models\JobFunction;
use App\Models\PolicyAssignment;

class PolicyBookController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->role == 'admin') {
                // Retrieve all policy books
                $policyBooks = PolicyBook::with(['category', 'policies', 'jobFunctions'])->get();
            }

            if ($user->role == 'user') {
                $userId = $user->id;

                $policyBooks = PolicyBook::where(function ($query) use ($userId) {
                    // Retrieve PolicyBooks assigned to the user through job functions
                    $query->whereHas('jobFunctions.users', function ($subQuery) use ($userId) {
                        $subQuery->where('users.id', $userId);
                    });

                    // Retrieve PolicyBooks assigned to the user through the PolicyAssignment table
                    $query->orWhereHas('policyAssignments', function ($subQuery) use ($userId) {
                        $subQuery->where('user_id', $userId);
                    });
                })
                    // Retrieve PolicyBooks marked as "general"
                    ->orWhere('general', true)
                    ->get();
            }

            return response()->json($policyBooks);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate and store a new policy book
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string|max:255',
                'category_id' => 'required|exists:categories,id',
                'effective_from_date' => 'required|date',
                'general' => 'boolean',
                'status' => 'required|in:published,draft,unpublished',
            ]);

            $policyBook = PolicyBook::create($validatedData);

            $policyBook->updatePolicyAssignments($request->jobFunctions);
            $policyBook->jobFunctions()->sync($request->jobFunctions);

            $policyBook->save();

            if ($request->policies) {

                $policies = $request->policies;

                foreach ($policies as $policy) {
                    $newPolicy = Policy::create([
                        'name' => $policy['name'],
                        'content' => $policy['content'],
                        'policy_book_id' => $policyBook->id, // Associate with the policy book
                        'created_by' => $request->user()->id, // Set the creator user ID
                        'modified_by' => $request->user()->id, // You can set this to null or handle it as needed
                    ]);
                    $newPolicy->save();
                }
            }

            if ($request->general == true) {
                $policyBook->markGeneral();
            }

            return response()->json(['message' => 'Policy book created successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage(), 'data' => $request->effective_from_date], 500);
        }
    }

    public function show($id)
    {
        try {
            // Return a specific policy book
            $policyBook = PolicyBook::with(['policies', 'category', 'jobFunctions'])->findOrFail($id);

            return response()->json($policyBook);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate and update a specific policy book
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string|max:255',
                'category_id' => 'required|exists:categories,id',
                'effective_from_date' => 'required|date',
                'general' => 'boolean',
                'status' => 'required|in:published,draft,unpublished',
            ]);

            $policyBook = PolicyBook::findOrFail($request->id);
            $generalFlag = $policyBook->general;

            $policyBook->update($validatedData);

            $policyBook->updatePolicyAssignments($request->jobFunctions);
            $policyBook->jobFunctions()->sync($request->jobFunctions);

            $policies = $request->policies;

            foreach ($policies as $policy) {
                if ($policy['id'] != 0) {
                    $newPolicy = Policy::findOrFail($policy['id']);
                    $newPolicy->update($policy);

                    $newPolicy->modified_by = $request->user()->id;
                    $newPolicy->save();
                } else {
                    $newPolicy = Policy::create([
                        'name' => $policy['name'],
                        'content' => $policy['content'],
                        'policy_book_id' => $policyBook->id, // Associate with the policy book
                        'created_by' => $request->user()->id, // Set the creator user ID
                        'modified_by' => $request->user()->id, // You can set this to null or handle it as needed
                    ]);
                    $newPolicy->save();
                }
            }

            if ($request->general == true) {
                $policyBook->markGeneral();
            }
            if ($request->general == false && $request->general != $generalFlag) {
                $policyBook->unMarkGeneral();
            }

            return response()->json(['message' => 'Policy book updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed, handle the exception
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage(), 'data' => $request->id], 500);
        }
    }

    public function delete($id)
    {
        try {
            // Delete a specific policy book
            $policyBook = PolicyBook::findOrFail($id);

            $policyBook->delete();

            return response()->json(['message' => 'Policy book deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    function data(Request $request)
    {
        $departments = Department::where('status', b'1')->get();

        $job_functions = JobFunction::where('status', b'1')->get();

        $categories = Category::where('status', b'1')->where('type', 'Policy')->get();

        return response()->json([
            'departments' => $departments,
            'job_functions' => $job_functions,
            'categories' => $categories,
            'user' => $request->user()
        ], 200);
    }

    function agreement(Request $request)
    {
        try {
            $policyAssignment = PolicyAssignment::findOrFail($request->id);
            if ($policyAssignment) {
                $policyAssignment->acknowledged = true;
                $policyAssignment->acknowledged_at = now();
                $policyAssignment->save();
            }
            return response()->json(['message' => 'Policy book acknowledged successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    function assigned(Request $request)
    {
        try {
            $user = $request->user();

            $assignedPolices = PolicyAssignment::where('user_id', $user->id)->with(['policyBook', 'policyBook.policies'])->get();

            return response()->json($assignedPolices);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }

    function showAssigned($id)
    {
        try {
            $assignedPolicy = PolicyAssignment::with('policyBook', 'policyBook.policies')->findOrFail($id);

            return response()->json($assignedPolicy);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }
}
