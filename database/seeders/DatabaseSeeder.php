<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $department = \App\Models\Department::create([
            'name' => 'Test Department',
            'description' => 'This is a desc.'
        ]);


        $jobFunction = \App\Models\JobFunction::create([
            'name' => 'Test Job Function',
            'department_id' => $department->id
        ]);

        $jobFunction2 = \App\Models\JobFunction::create([
            'name' => 'Test Job Function 2',
            'department_id' => $department->id
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Test Category',
            'type' => 'SOP',
        ]);

        $user = \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'department_id' => $department->id,
        ]);

        $user->jobFunctions()->sync([$jobFunction->id]);

        $user2 = \App\Models\User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'department_id' => $department->id,
        ]);

        $user2->jobFunctions()->sync([$jobFunction2->id]);

        $sop = \App\Models\SOP::create([
            'name' => 'Test Sop 1',
            'category_id' => $category->id,
            'department_id' => $department->id,
            'content' => 'THIS IS A DUMMY CONTENT FOR SOP 1',
            'created_by' => $user->id,
            'edited_by' => $user->id,
        ]);

        $sop->jobFunctions()->sync([$jobFunction->id, $jobFunction2->id]);


        $sop2 = \App\Models\SOP::create([
            'name' => 'Test Sop 2',
            'category_id' => $category->id,
            'department_id' => $department->id,
            'content' => 'THIS IS A DUMMY CONTENT FOR SOP 2',
            'created_by' => $user->id,
            'edited_by' => $user->id,
        ]);

        $sop2->jobFunctions()->sync([$jobFunction->id]);


    }
}
