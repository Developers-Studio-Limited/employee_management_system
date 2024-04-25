<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $role = Role::create(['name' => 'admin']);
        $permissions = [
            ['name' => 'view employees list'],
            ['name' => 'create employee'],
            ['name' => 'edit employee'],
            ['name' => 'delete employee'],
            ['name' => 'create department'],
            ['name' => 'assign salary'],
            ['name' => 'edit salary'],
            ['name' => 'edit department'],
            ['name' => 'leave approved'],
            ['name' => 'un-approved leaves'],
        ];

        foreach ($permissions as $key => $permission) {
            Permission::create($permission);
            $role->givePermissionTo(Permission::findByName($permission['name']));
        }

        $user = User::findOrFail(1);
        $user->assignRole('admin');

    }
}
