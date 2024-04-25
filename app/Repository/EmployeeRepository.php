<?php

namespace App\Repository;

use App\Repository\Interface\EmployeeRepositoryInterface;
use App\Models\Salary;
use App\Models\Leave;
use Carbon\Carbon;

use function App\errorLogs;

class EmployeeRepository implements EmployeeRepositoryInterface {

    /**
     * Apply for leave.
     *
     * @param array $data Data for applying leave.
     * @param int $id ID of the employee applying for leave.
     * @return \App\Models\Leave|null Leave instance if successful, null otherwise.
     */
    public function applyLeave($data, $id) {
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
    
            $leave = new Leave;
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

    /**
     * Get salary details for an employee.
     *
     * @param int $data ID of the employee.
     * @return \Illuminate\Http\JsonResponse JSON response containing salary details.
     */
    public function empSalary($data){
        try {
            $salary = Salary::selectRaw('total_amount as total')
            ->where('employee_id', $data)
                ->whereBetween('effective_date', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])->get();
                if (!$salary) {
                    return response()->request_response(404, false, "The salary for this month has not been issued to you yet.");
                }
            return $salary;
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
