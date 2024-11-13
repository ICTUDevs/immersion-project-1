<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Vinkla\Hashids\Facades\Hashids;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemController extends Controller
{
    public function user(Request $request)
    {
        $users = User::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        })->with('roles')->paginate(10);

        $users->getCollection()->transform(function ($user) {
            $user->hashed_id = Hashids::encode($user->id);
            return $user;
        });

        return inertia('Modules/System/User/Index', [
            'users' => $users,
        ]);
    }

    public function editUser($hashed_id)
    {
        $id = Hashids::decode($hashed_id)[0];
        $user = User::findOrFail($id);
        $role = Role::all();
        $userRoles = $user->getRoleNames();
        return inertia('Modules/System/User/Edit', [
            'user' => $user,
            'roles' => $role,
            'userRoles' => $userRoles,
            'hashed_id' => $hashed_id,
        ]);
    }

    public function updateUser(Request $request, $hashed_id)
    {
        sleep(1);
        $id = Hashids::decode($hashed_id)[0];
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('system.user')->with('message', 'User updated.');
    }

    public function permission(Request $request)
    {
        $permissions = Permission::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })->paginate(10);

        $permissions->getCollection()->transform(function ($permission) {
            $permission->hashed_id = Hashids::encode($permission->id);
            return $permission;
        });

        return inertia('Modules/System/Permission/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function role(Request $request)
    {
        $roles = Role::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })->paginate(10);

        $roles->getCollection()->transform(function ($role) {
            $role->hashed_id = Hashids::encode($role->id);
            return $role;
        });

        return inertia('Modules/System/Role/Index', [
            'roles' => $roles,
        ]);
    }

    public function createPermission()
    {
        return inertia('Modules/System/Permission/Create');
    }

    public function createRole()
    {
        return inertia('Modules/System/Role/Create');
    }

    public function editPermission($hashed_id)
    {
        $id = Hashids::decode($hashed_id)[0];
        $permissions = Permission::findOrFail($id);
        return inertia('Modules/System/Permission/Edit', [
            'permissions' => $permissions,
            'hashed_id' => $hashed_id,
        ]);
    }

    public function storePermission(Request $request)
    {
        sleep(1);
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect()->route('system.permission')->with('message', 'Permission created.');
    }

    public function updatePermission(Request $request, $hashed_id)
    {
        sleep(1);
        $id = Hashids::decode($hashed_id)[0];
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
        ]);

        return redirect()->route('system.permission')->with('message', 'Permission updated.');
    }

    public function storeRole(Request $request)
    {
        sleep(1);
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect()->route('system.role')->with('message', 'Role created.');
    }

    public function editRole($hashed_id)
    {
        $id = Hashids::decode($hashed_id)[0];
        $roles = Role::findOrFail($id);
        return inertia('Modules/System/Role/Edit', [
            'roles' => $roles,
            'hashed_id' => $hashed_id,
        ]);
    }

    // Edit Permission View
    public function viewPermission($hashedId)
    {
        $id = Hashids::decode($hashedId)[0];
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return inertia('Modules/System/Role/Permission', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
            'hashedId' => $hashedId
        ]);
    }

    // Update Permission
    public function assignPermission(Request $request, $hashedId)
    {
        sleep(1);
        $id = Hashids::decode($hashedId)[0];

        $role = Role::findOrFail($id);

        $role->syncPermissions($request->permission_id);

        return redirect()->route('system.role')->with('message', 'Permission assigned.');
    }
}
