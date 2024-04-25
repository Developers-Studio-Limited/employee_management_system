<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalaryRequest;
use App\Services\SalaryService;
use Exception;

use function App\errorLogs;

class SalaryController extends Controller
{
    private $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * Create a new salary entry for the specified employee.
     * Method: POST
     *
     * @param SalaryRequest $salaryRequest The request object containing salary details.
     * @param int $employeeId The ID of the employee for whom the salary is being created.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the status of the operation.
     * @throws \Exception If an error occurs during the salary creation process.
     */
    public function create(SalaryRequest $salaryRequest, $employeeId) {
        try {
            $salary = $this->salaryService->distributeSalary($salaryRequest, $employeeId);

            $statusCode = $salary ? 200 : 404;
            $success = $salary ? true : false;
            $message = $salary ? "The salary was successfully added." : "Something went wrong salary has not been added";
            return response()->request_response($statusCode, $success, $message, $salary);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }

    /**
     * Update the existing salary entry for the specified employee.
     * Method: POST

     * @param SalaryRequest $salaryRequest The request object containing updated salary details.
     * @param int $employeeId The ID of the employee for whom the salary is being updated.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the status of the operation.
     * @throws \Exception If an error occurs during the salary update process or if the employee is not found.
     */
    public function update(SalaryRequest $salaryRequest, $employeeId) {
        try {
            $updatedSalary = $this->salaryService->updateSalary($salaryRequest, $employeeId);

            $responseCode = $updatedSalary ? 200 : 404;
            $success = $updatedSalary ? true : false;
            $responseMessage = $updatedSalary ? "Salary Update successfully" : "Salary cannot be updated; something went wrong";

            return response()->request_response($responseCode, $success, $responseMessage, $updatedSalary);

        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
