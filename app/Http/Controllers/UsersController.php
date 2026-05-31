<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dropdown;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::select([
                'users.id',
                'users.name',
                'users.username',
                'users.email',
                'users.role',
                'users.status',
                'users.is_active',
                'users.last_login',
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('last_login', function ($user) {
                    return $user->last_login
                        ? $user->last_login->format('d-m-Y H:i:s')
                        : 'Never';
                })
                ->editColumn('is_active', function ($user) {
                    return $user->is_active
                        ? '<span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Active</span>'
                        : '<span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">Inactive</span>';
                })
                ->addColumn('action', function ($user) {
                    $editBtn = '
                        <button
                            type="button"
                            class="btn-edit-user inline-flex items-center rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark"
                            data-id="' . $user->id . '"
                            data-name="' . e($user->name) . '"
                            data-username="' . e($user->username) . '"
                            data-email="' . e($user->email) . '"
                            data-role="' . e($user->role) . '">
                            Edit
                        </button>
                    ';

                    if ($user->is_active) {
                        $statusBtn = '
                            <button
                                type="button"
                                class="btn-revoke-user inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700"
                                data-id="' . $user->id . '"
                                data-name="' . e($user->name) . '">
                                Revoke
                            </button>
                        ';
                    } else {
                        $statusBtn = '
                            <button
                                type="button"
                                class="btn-activate-user inline-flex items-center rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-green-700"
                                data-id="' . $user->id . '"
                                data-name="' . e($user->name) . '">
                                Activate
                            </button>
                        ';
                    }

                    return '<div class="flex items-center gap-2">' . $editBtn . $statusBtn . '</div>';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        $roles = Dropdown::query()
            ->where('category', 'role')
            ->orderBy('name_value')
            ->get(['name_value', 'code_format']);

        return view('user.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role'     => ['required', 'string', 'max:255'],
        ]);

        User::create([
            'name'                => $request->name,
            'username'            => $request->username,
            'email'               => $request->email,
            'password'            => $request->password,
            'role'                => $request->role,
            'status'              => 'ACTIVE',
            'is_active'           => true,
            'login_counter'       => 0,
            'password_changed_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'User created successfully',
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'max:255'],
            'role'     => ['required', 'string', 'max:255'],
        ]);

        $payload = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $request->role,
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->password;
            $payload['password_changed_at'] = now();
        }

        $user->update($payload);

        return response()->json([
            'ok' => true,
            'message' => 'User updated successfully',
        ]);
    }

    public function revoke($id)
    {
        User::where('id', $id)->update([
            'is_active' => false,
            'status'    => 'INACTIVE',
        ]);

        return response()->json(['ok' => true]);
    }

    public function activate($id)
    {
        User::where('id', $id)->update([
            'is_active' => true,
            'status'    => 'ACTIVE',
        ]);

        return response()->json(['ok' => true]);
    }

    public function get($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'role'     => $user->role,
            'status'   => $user->status,
            'is_active' => $user->is_active,
        ]);
    }
}
