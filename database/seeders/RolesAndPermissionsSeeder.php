<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'gestionar_sistemas',
            'gestionar_usuarios',
            'crear_equipo',
            'gestionar_equipo',
            'jugar',
            'crear_encuentro_libre',
            'gestionar_cancha'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // create roles and assign created permissions
        $roleJugador = Role::firstOrCreate(['name' => 'jugador', 'guard_name' => 'api']);
        $roleJugador->syncPermissions(['jugar', 'crear_equipo', 'crear_encuentro_libre']);

        $roleCapitan = Role::firstOrCreate(['name' => 'capitan', 'guard_name' => 'api']);
        $roleCapitan->syncPermissions(['jugar', 'crear_equipo', 'gestionar_equipo', 'crear_encuentro_libre']);

        $roleCancha = Role::firstOrCreate(['name' => 'dueño_cancha', 'guard_name' => 'api']);
        $roleCancha->syncPermissions(['gestionar_cancha', 'crear_encuentro_libre']);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $roleAdmin->syncPermissions(Permission::all());

        // Create Default Users and assign Roles

        // 1. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@prueba.com'],
            [
                'nombre' => 'Administrador',
                'password' => Hash::make('admin'),
                'estado' => 'activo'
            ]
        );
        if (!$admin->playerProfile) {
            $admin->playerProfile()->create([]);
            $admin->playerStats()->create([]);
        }
        $admin->assignRole('admin');

        // 2. Capitán
        $capitan = User::firstOrCreate(
            ['email' => 'capitan@prueba.com'],
            [
                'nombre' => 'Capitán Prueba',
                'password' => Hash::make('capitan'),
                'estado' => 'activo'
            ]
        );
        if (!$capitan->playerProfile) {
            $capitan->playerProfile()->create([]);
            $capitan->playerStats()->create([]);
        }
        $capitan->assignRole('capitan');

        // 3. Dueño Cancha
        $cancha = User::firstOrCreate(
            ['email' => 'campo@prueba.com'],
            [
                'nombre' => 'Dueño Cancha',
                'password' => Hash::make('campo'),
                'estado' => 'activo'
            ]
        );
        if (!$cancha->playerProfile) {
            $cancha->playerProfile()->create([]);
            $cancha->playerStats()->create([]);
        }
        $cancha->assignRole('dueño_cancha');
    }
}
