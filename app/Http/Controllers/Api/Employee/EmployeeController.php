<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Services\EmployeeService;

use function App\errorLogs;

class EmployeeController extends Controller
{
    public $employeeService;
    
    /**
     * Constructor to inject EmployeeService dependency.
     *
     * @param EmployeeService $employeeService The EmployeeService instance.
     */
     public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    
    /**
     * Apply for leave.
     *
     * Method: POST
     * @param \App\Http\Requests\LeaveRequest $leaveRequest The leave request object.
     * @param int $employeeId The ID of the employee applying for leave.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of leave application.
     */
    public function applyLeave(LeaveRequest $leaveRequest, $employeeId) {
        try {
            $appliedLeave = $this->employeeService->applyLeave($leaveRequest->validated(), $employeeId);
            return response()->request_response(200, true, "Leave application has been initiated.", $appliedLeave);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * View salary details.
     *
     * Method: GET
     * @return \Illuminate\Http\JsonResponse A JSON response containing salary details.
     */
    public function viewSalary() {
        try {
            $empData = auth()->guard('employee-api')->user()->id;
            $salaries = $this->employeeService->viewEmployeeSalary($empData);
            return response()->request_response(200, true, "You received you this month salary", $salaries);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
