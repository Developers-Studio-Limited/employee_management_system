<?php

namespace App\Repository;

use App\Repository\Interface\SalaryRepositoryInterface;
use App\Models\Salary;
use App\Models\Leave;
use Carbon\Carbon;

use function App\errorLogs;

class SalaryRepository implements SalaryRepositoryInterface {
    
    /**
     * Create a new salary record for an employee.
     *
     * @param array $data Data for creating a new salary record.
     * @param int $employeeId ID of the employee.
     * @return \App\Models\Salary|null Newly created salary instance if successful, null otherwise.
     */
    public function createSalary($data, $employeeId) {
        try {
            $currentMonth = date('Y-m');
            $leaveDeducted = Salary::where('employee_id', $employeeId)->where('effective_date', 'like', $currentMonth . '%')->exists();
            $leaveRecords = Leave::where('employee_id', $employeeId)->get();
    
            if ($leaveDeducted) {
                return response()->request_response(200, false, "Money has already been deducted for the current month.");
            }
    
            $totalLeaveDays = 0;
            foreach ($leaveRecords as $leave) {
                $startDate = Carbon::parse($leave->start_date);
                $endDate = Carbon::parse($leave->end_date);
                $leaveDuration = $endDate->diffInDays($startDate) + 1;
                $totalLeaveDays += $leaveDuration;
            }
            
            $dailySalary = $data['amount'];
            $monthlySalary = $dailySalary * 30;
            $allowance = $data['allowance'];
    
            $salary = new Salary;
            $salary->employee_id = $employeeId;
            $salary->amount = $dailySalary;
            $salary->allowance = $allowance; 
            $salary->deduction = 0; 
            $totalAmount = (int)$monthlySalary + (int)$allowance;
    
            if ($totalLeaveDays >= 2) {
                $deduction = ($totalLeaveDays - 2) * $dailySalary;
                $salary->deduction = $deduction;
                $totalAmount -= $deduction;
            } else {
                $deduction = 0;
            }
            $salary->total_amount = $totalAmount;
            $salary->effective_date = now();
            $salary->leave_days = $totalLeaveDays > 0 ? $totalLeaveDays : 0;
            $salary->save();
            return $salary;
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * Update the salary record for an employee.
     *
     * @param array $data Data for updating the salary record.
     * @param int $employeeId ID of the employee.
     * @return \App\Models\Salary|null Updated salary instance if successful, null otherwise.
     */
    public function updateSalary($data, $employeeId) {
        try {
            $salary = Salary::where('employee_id', $employeeId)->firstOrFail();
            if ($salary) {
                $leaveRecords = Leave::where('employee_id', $employeeId)->get();
        
                $totalLeaveDays = 0;
                foreach ($leaveRecords as $leave) {
                    $startDate = Carbon::parse($leave->start_date);
                    $endDate = Carbon::parse($leave->end_date);
                    $leaveDuration = $endDate->diffInDays($startDate) + 1;
                    $totalLeaveDays += $leaveDuration;
                }
               
                $dailySalary = $data['amount'];
                $monthlySalary = $dailySalary * 30;
                $allowance = $data['allowance'];
                $totalAmount = (int)$monthlySalary + (int)$allowance;
        
                $salary->amount = $dailySalary;
                $salary->allowance = $allowance; 
                $salary->deduction = $data['deduction'] ?? 0; 
                
                if ($totalLeaveDays >= 2) {
                    $deduction = ($totalLeaveDays - 2) * $dailySalary;
                    $salary->deduction = $deduction;
                    $totalAmount -= $deduction;
                }
                $salary->total_amount = $totalAmount;
                    
                $salary->effective_date = now();
                $salary->leave_days = $totalLeaveDays > 0 ? $totalLeaveDays : 0;
                $salary->update();
                return $salary;
        }
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}