<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\DepartmentRequest;
use App\Http\Controllers\Controller;
use App\Services\DepartmentService;

use function App\errorLogs;

class DepartmentController extends Controller
{
    protected  $departmentService;
    public function __construct(DepartmentService  $service)
    {
        $this->departmentService = $service;
    }

    /**
     * Create Department
     * Method: POST
     * @param DepartmentRequest $requst the request contianing department data
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function create(DepartmentRequest $departmentRequest) {
        try {
            $department = $this->departmentService->createDepartment($departmentRequest->validated());
            return response()->request_respone(201, true, 'Department created Successfully', $department);
        } catch (\Exception $ex) {
            return  errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    /**
     * Update Departments
     * Method : POST
     * @param integer $id The id of the department to be updated.
     * @param DepartmentRequest $request Containing new values for the department.
     * @return \Illuminate\Http\JsonResponse JSON Response with a message and status
     */
    public function update(DepartmentRequest  $request ,int $id) {
       try{
            $updated_department = $this->departmentService->updateDepartment($request->validated() ,$id );
            $statusCode = $updated_department ? 201 : 404;
            $success = $updated_department ? true : false;
            $message = $updated_department ? "Department Updated successfully" : "Department Not Found";
            return response()->request_response($statusCode, $success, $message, $updated_department);

        }catch(\Exception $ex){
           return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }
}
