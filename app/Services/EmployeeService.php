<?php

namespace App\Services;

use App\Repository\Interface\EmployeeRepositoryInterface;

use function App\errorLogs;

class EmployeeService {

    protected $employeeRepositoryInterface;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
    }

    public function viewEmployeeSalary($data) {
        try {
            return $this->employeeRepositoryInterface->empSalary($data);
        } catch (\Exception $ex) {
            return  errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }

    public function applyLeave($data, $id){
        try {
            return  $this->employeeRepositoryInterface->applyLeave($data,$id);
        } catch (\Exception $ex) {
            return  errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}