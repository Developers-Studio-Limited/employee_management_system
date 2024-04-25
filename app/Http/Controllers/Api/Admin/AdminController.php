<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\EmployeeRequest;
use App\Http\Controllers\Controller;
use App\Services\AdminService;

use function App\errorLogs;

class AdminController extends Controller
{
    public $adminService;
    
    /**
     * Constructor to inject EmployeeService dependency.
     *
     * @param EmployeeService $employeeService The EmployeeService instance.
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }
    
    /**
     * Retrieves all employees.
     *
     * Method: GET
     * @return \Illuminate\Http\JsonResponse JSON response with all employees.
     */
    public function index() {
        try {
            $employees = $this->adminService->getAllEmployees();
            return response()->request_response(200, true, "Fetch all the Employee", $employees);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * Creates a new employee.
     *
     * Method: POST
     * @param EmployeeRequest $request The request containing employee data.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function create(EmployeeRequest $request) {
        try {
            $employee = $this->adminService->createEmployee($request->validated());
            return response()->request_response(201, true, "Employee created successfully", $employee);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * Retrieves an employee by ID.
     *
     * Method: GET
     * @param int $id The ID of the employee.
     * @return \Illuminate\Http\JsonResponse JSON response with employee data or error message.
     */
    public function show($id) {
        try {
            $employeeById = $this->adminService->getEmployeeById($id);
            $statusCode = $employeeById ?  200 : 404;
            $success = $employeeById ? true : false;
            $message = $employeeById ? " Employee retrieved successfully" : "No record found";
            return response()->request_response($statusCode, $success, $message, $employeeById);

        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * Updates an employee.
     *
     * Method: PUT
     * @param EmployeeRequest $request The request containing updated employee data.
     * @param int $id The ID of the employee to update.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function update(EmployeeRequest $request, $id) {
        try {
            $data = $request->validated();
            $updatedEmployee = $this->adminService->updateEmployee($data, $id);
            $response = [
                'statusCode' => $updatedEmployee ? 200 : 404,
                'status' => $updatedEmployee ? true : false,
                'message' => $updatedEmployee ? 'Employee updated successfully' : 'Employee not found or update failed'
            ];
        
            if ($updatedEmployee) {
                $response['data'] = $updatedEmployee;
            }
        
            return response()->request_response($response['StatusCode'],$response['status'], $response['message'], $updatedEmployee ? $updatedEmployee : null);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
    
    /**
     * Deletes an employee.
     *
     * Method: GET
     * @param int $id The ID of the employee to delete.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function delete($id) {
        try {

            $this->adminService->deleteEmployee($id);
            return response()->request_response(204, true, "Successfully deleted employee");

        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    /**
     * Restores an employee.
     *
     * Method: GET
     * @param int $id The ID of the employee to restore.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function restore($id) {
        try {
            $restoredEmployee = $this->adminService->restoreEmployee($id);
            
            $statusCode = $restoredEmployee ? 204 : 404;
            $success = $restoredEmployee ? true : false;
            $message = $restoredEmployee ? "Successfully Restored employee" : "Employee not found";

            return response()->request_response($statusCode, $success, $message, $restoredEmployee);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
