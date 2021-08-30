<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('model_has_roles')->insert([
            [
                'role_id' => 1,
                'model_id' => 1,
                'model_type' => User::class,
            ],
            [
                'role_id' => 2,
                'model_id' => 2,
                'model_type' => User::class,
            ],
            [
                'role_id' => 1,
                'model_id' => 3,
                'model_type' => User::class,
            ],
            [
                'role_id' => 2,
                'model_id' => 4,
                'model_type' => User::class,
            ],
            [
                'role_id' => 1,
                'model_id' => 5,
                'model_type' => User::class,
            ],
            [
                'role_id' => 2,
                'model_id' => 6,
                'model_type' => User::class,
            ],
            [
                'role_id' => 1,
                'model_id' => 7,
                'model_type' => User::class,
            ],
            [
                'role_id' => 2,
                'model_id' => 8,
                'model_type' => User::class,
            ],
            [
                'role_id' => 1,
                'model_id' => 9,
                'model_type' => User::class,
            ],
            [
                'role_id' => 1,
                'model_id' => 10,
                'model_type' => User::class,
            ],
        ]);
    }
}
