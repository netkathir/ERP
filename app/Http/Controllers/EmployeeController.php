<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('employees', 'view')) {
            abort(403, 'You do not have permission to view employees.');
        }

        $query = Employee::with(['department', 'designation']);
        $query = $this->applyBranchFilter($query, Employee::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('department', function($deptQuery) use ($search) {
                      $deptQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('designation', function($desigQuery) use ($search) {
                      $desigQuery->where('name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        switch ($sortBy) {
            case 'employee_code': $query->orderBy('employees.employee_code', $sortOrder); break;
            case 'name': $query->orderBy('employees.name', $sortOrder); break;
            case 'email': $query->orderBy('employees.email', $sortOrder); break;
            case 'department': 
                $query->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                      ->orderBy('departments.name', $sortOrder)
                      ->select('employees.*')
                      ->distinct();
                break;
            case 'designation':
                $query->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                      ->orderBy('designations.name', $sortOrder)
                      ->select('employees.*')
                      ->distinct();
                break;
            default: $query->orderBy('employees.id', $sortOrder); break;
        }
        $employees = $query->paginate(15)->withQueryString();
        return view('masters.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('employees', 'create')) {
            abort(403, 'You do not have permission to create employees.');
        }

        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        
        // Generate employee code
        $employeeCode = $this->generateEmployeeCode();
        
        // Indian states list
        $states = $this->getIndianStates();
        
        // Countries list
        $countries = $this->getCountries();

        return view('masters.employees.create', compact('departments', 'designations', 'employeeCode', 'states', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('employees', 'create')) {
            abort(403, 'You do not have permission to create employees.');
        }

        $request->validate([
            'employee_code' => 'required|string|max:255|unique:employees,employee_code',
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'date_of_birth' => 'required|date',
            'email' => 'required|email|unique:employees,email',
            'mobile_no' => 'required|string|max:20|regex:/^[0-9+\-() ]+$/',
            'active' => 'required|in:Yes,No',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'emergency_contact_no' => 'nullable|string|max:20|regex:/^[0-9+\-() ]+$/',
        ], [
            'employee_code.required' => 'Employee Code is required.',
            'employee_code.unique' => 'This Employee Code already exists.',
            'name.required' => 'Employee Name is required.',
            'department_id.required' => 'Department is required.',
            'designation_id.required' => 'Designation is required.',
            'date_of_birth.required' => 'Date of Birth is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'mobile_no.required' => 'Mobile Number is required.',
            'mobile_no.regex' => 'Please enter a valid mobile number.',
            'active.required' => 'Active status is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'country.required' => 'Country is required.',
            'pincode.regex' => 'Pincode must be 6 digits.',
            'emergency_contact_no.regex' => 'Please enter a valid emergency contact number.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        
        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('employees', 'edit')) {
            abort(403, 'You do not have permission to edit employees.');
        }

        $query = Employee::query();
        $query = $this->applyBranchFilter($query, Employee::class);
        $employee = $query->findOrFail($id);
        
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        
        // Indian states list
        $states = $this->getIndianStates();
        
        // Countries list
        $countries = $this->getCountries();

        return view('masters.employees.edit', compact('employee', 'departments', 'designations', 'states', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('employees', 'edit')) {
            abort(403, 'You do not have permission to edit employees.');
        }

        $query = Employee::query();
        $query = $this->applyBranchFilter($query, Employee::class);
        $employee = $query->findOrFail($id);

        $request->validate([
            'employee_code' => 'required|string|max:255|unique:employees,employee_code,' . $id,
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'date_of_birth' => 'required|date',
            'email' => 'required|email|unique:employees,email,' . $id,
            'mobile_no' => 'required|string|max:20|regex:/^[0-9+\-() ]+$/',
            'active' => 'required|in:Yes,No',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'emergency_contact_no' => 'nullable|string|max:20|regex:/^[0-9+\-() ]+$/',
        ], [
            'employee_code.required' => 'Employee Code is required.',
            'employee_code.unique' => 'This Employee Code already exists.',
            'name.required' => 'Employee Name is required.',
            'department_id.required' => 'Department is required.',
            'designation_id.required' => 'Designation is required.',
            'date_of_birth.required' => 'Date of Birth is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'mobile_no.required' => 'Mobile Number is required.',
            'mobile_no.regex' => 'Please enter a valid mobile number.',
            'active.required' => 'Active status is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'country.required' => 'Country is required.',
            'pincode.regex' => 'Pincode must be 6 digits.',
            'emergency_contact_no.regex' => 'Please enter a valid emergency contact number.',
        ]);

        $data = $request->all();
        
        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('employees', 'delete')) {
            abort(403, 'You do not have permission to delete employees.');
        }

        $query = Employee::query();
        $query = $this->applyBranchFilter($query, Employee::class);
        $employee = $query->findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    /**
     * Get designations by department (AJAX)
     */
    public function getDesignations(Request $request)
    {
        $departmentId = $request->get('department_id');
        
        if (!$departmentId) {
            return response()->json([]);
        }

        $designations = Designation::where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($designations);
    }

    /**
     * Generate unique employee code
     */
    private function generateEmployeeCode()
    {
        // Get the last employee code
        $lastEmployee = Employee::orderBy('id', 'desc')->first();
        
        if ($lastEmployee && preg_match('/^([A-Z]+)(\d+)$/', $lastEmployee->employee_code, $matches)) {
            $prefix = $matches[1];
            $number = intval($matches[2]);
            $newNumber = $number + 1;
            return $prefix . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        }
        
        // Default format: EMP01, EMP02, etc.
        $count = Employee::count();
        return 'EMP' . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get Indian states list
     */
    private function getIndianStates()
    {
        return [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
            'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
            'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
            'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
            'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
        ];
    }

    /**
     * Get countries list
     */
    private function getCountries()
    {
        return [
            'India', 'United States', 'United Kingdom', 'Canada', 'Australia',
            'Germany', 'France', 'Italy', 'Spain', 'Japan', 'China', 'Brazil',
            'Russia', 'South Korea', 'Mexico', 'Indonesia', 'Netherlands',
            'Saudi Arabia', 'Turkey', 'Switzerland', 'Sweden', 'Belgium',
            'Argentina', 'Norway', 'Poland', 'Thailand', 'South Africa',
            'United Arab Emirates', 'Malaysia', 'Singapore', 'Philippines'
        ];
    }
}
