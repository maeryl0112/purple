<?php

namespace App\Http\Controllers;

use App\Enums\UserRolesEnum;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $request->validate([
        'search' => 'nullable|string|max:255',
        'role' => 'nullable|string|in:employee,customer,admin', 
    ]);

    $search = $request->input('search');
    $role = $request->input('role');
    $user = auth()->user(); // Get logged-in user

    $query = User::with('branch', 'role'); // Eager load relationships

    // Restrict employees to see only users from their assigned branch
    if ($user->role_id == UserRolesEnum::Employee) {
        $query->where('branch_id', $user->branch_id);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone_number', 'LIKE', "%{$search}%");
        });
    }

    if ($role) {
        $roleId = match ($role) {
            'employee' => UserRolesEnum::Employee,
            'customer' => UserRolesEnum::Customer,
            'admin' => UserRolesEnum::Admin,
            default => null,
        };

        if ($roleId !== null) {
            $query->where('role_id', $roleId);
        }
    }

    $users = $query->paginate(10);

    return view('dashboard.manage-users.index', compact('users', 'search', 'role'));
}

    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        $employees = Employee::whereDoesntHave('user')->get(); // Fetch employees without accounts

    
        return view('dashboard.manage-users.create-user', compact('branches', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $role = $request->input('role');
    $user = auth()->user(); // Get the logged-in user

    $rules = [
        'password' => 'required|string|min:8|max:255',
        'password_confirmation' => 'required|string|min:8|max:255|same:password',
        'role' => 'required|string|in:employee,customer',
    ];

    if ($role === 'employee') {
        $rules['employee_id'] = 'required|exists:employees,id';
    } else {
        $rules += [
            'name' => 'required|string|min:1|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required', 'string', 'regex:/^\+[1-9]{1}[0-9]{3,14}$/', 'unique:users',
        ];

        // Ensure branch_id is required only for admins
        if ($user->role_id == UserRolesEnum::Admin) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->route('manageusers.create')
            ->withErrors($validator)
            ->withInput();
    }

    try {
        if ($role === 'employee') {
            $employee = Employee::findOrFail($request->employee_id);

            User::create([
                'name' => $employee->first_name,
                'email' => $employee->email,
                'phone_number' => $employee->phone_number,
                'password' => Hash::make($request->password),
                'role_id' => UserRolesEnum::Employee,
                'branch_id' => $employee->branch_id,
            ]);
        } else {
            // If an employee is creating a customer, set the branch automatically
            $branchId = $user->role_id == UserRolesEnum::Employee ? $user->branch_id : $request->branch_id;

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role_id' => UserRolesEnum::Customer,
                'branch_id' => $branchId, // Assign branch automatically if employee
            ]);
        }
    } catch (Exception $e) {
        return redirect()->route('manageusers')->with('errormsg', 'User creation failed.');
    }

    return redirect()->route('manageusers')->with('success', 'User created successfully.');
}

    

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        // find the appointments of the user
        $appointments = Appointment::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        return view('dashboard.manage-users.show-user', compact('user', 'appointments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
