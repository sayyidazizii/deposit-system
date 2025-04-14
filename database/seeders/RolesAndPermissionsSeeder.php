<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        $supervisorRole = Role::create(['name' => 'supervisor']);

        $createDepositPermission = Permission::create(['name' => 'create Deposit']);
        $editDepositPermission = Permission::create(['name' => 'edit Deposit']);
        $deleteDepositPermission = Permission::create(['name' => 'delete Deposit']);

        $adminRole->givePermissionTo([$createDepositPermission, $editDepositPermission, $deleteDepositPermission]);
        $userRole->givePermissionTo($createDepositPermission);
        $supervisorRole->givePermissionTo([$createDepositPermission, $editDepositPermission]);

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '085602678871',
            'password' => bcrypt('12345678'),
        ]);
        $admin->assignRole('admin');

        $user = User::create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'phone' => '085602678871',
            'password' => bcrypt('12345678'),
        ]);
        $user->assignRole('user');

        $supervisor = User::create([
            'name' => 'supervisor',
            'email' => 'supervisor@gmail.com',
            'phone' => '085602678871',
            'password' => bcrypt('12345678'),
        ]);
        $supervisor->assignRole('supervisor');

    }
}
