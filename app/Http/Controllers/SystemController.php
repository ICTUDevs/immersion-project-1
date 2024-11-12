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
                })
                ->paginate(10);

        return inertia('Modules/System/User/Index', [
            'users' => $users,
        ]);
    }

    public function permission(Request $request)
    {
        $permissions = Permission::when($request->search, function($query) use ($request){
                    $query->where('name', 'like', '%'.$request->search.'%');
                })->paginate(10);

                $permissions->getCollection()->transform(function($permission){
                    $permission->hashed_id = Hashids::encode($permission->id);
                    return $permission;
                });

        return inertia('Modules/System/Permission/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function role(Request $request)
    {
            $roles = Role::when($request->search, function($query) use ($request){
                        $query->where('name', 'like', '%'.$request->search.'%');
                    })->paginate(10);

                    $roles->getCollection()->transform(function($role){
                        $role->hashed_id = Hashids::encode($role->id);
                        return $role;
                    });

            return inertia('Modules/System/Permission/Index', [
                'roles' => $roles,
            ]);
    }

    public function createPermission()
    {
        return inertia('Modules/System/Permission/Create');
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
}
