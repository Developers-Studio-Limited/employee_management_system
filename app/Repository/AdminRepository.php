<?php

namespace App\Repository;

use App\Repository\Interface\AdminRepositoryInterface;
use App\Models\Employee;
use App\Models\Leave;

use function App\errorLogs;

class  AdminRepository implements AdminRepositoryInterface {

    /**
     * Retrieve all employees.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of all employees.
     */
    public function index() {
        try {
            return Employee::all();
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    /**
     * Create a new employee.
     *
     * @param array $data Data for creating a new employee.
     * @return \App\Models\Employee Newly created employee instance.
     */
    public function create($data)
    {
        try {
            return Employee::create($data);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

     /**
     * Retrieve an employee by ID.
     *
     * @param int $id ID of the employee.
     * @return \App\Models\Employee|null Employee instance if found, null otherwise.
     */
    public function show($id){
        try {
            return Employee::findOrfail($id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    /**
     * Update an existing employee.
     *
     * @param array $data Data for updating the employee.
     * @param int $id ID of the employee to be updated.
     * @return \App\Models\Employee|null Updated employee instance if successful, null otherwise.
     */
    public function update($data, $id)
    {
        try {
            $user = Employee::findOrFail($id);
            $user->update($data);
            return $user;
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    /**
     * Delete an employee.
     *
     * @param int $id ID of the employee to be deleted.
     * @return void
     */
    public function delete($id)
    {
        try {
            $employee =  Employee::findOrFail($id);
            $employee->delete();
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

     /**
     * Restore a soft deleted employee.
     *
     * @param int $id ID of the employee to be restored.
     * @return void
     */
    public function restore($id) {
        try {
            return Employee::where('id', $id)->withTrashed()->restore();
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    /**
     * Apply for leave.
     *
     * @param array $data Data for applying leave.
     * @param int $id ID of the employee applying for leave.
     * @return \App\Models\Leave|null Leave instance if successful, null otherwise.
     */
    public function applyLeave($data, $id)
    {
        try {
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
        
            $appliedLeaves = Leave::where('employee_id', $id)
                    ->where('start_date', '>=', $start_date)
                    ->where('end_date', '<=', $end_date)
                    ->count();
      
                if ($appliedLeaves >= 2) {
                    return response()->request_response(200, true, "You have already applied for more than two leaves.", $appliedLeaves);
                }
    
            $leave = new Leave();
            $leave->employee_id = $id;
            $leave->type = "paid";
            $leave->start_date = $data['start_date'];
            $leave->end_date = $data['end_date'];
            $leave->reason = $data['reason'];
            $leave->save();
            return  $leave;
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
}