<?php

namespace App\Services;

use App\Repository\Interface\EmployeeRepositoryInterface;

use function App\errorLogs;

class AdminService {
    
    protected $employeeRepositoryInterface;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
    }

    public function getAllEmployees() {
        try {
            return $this->employeeRepositoryInterface->index();
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
    
    public function createEmployee($data) {
        try {
            return $this->employeeRepositoryInterface->create($data);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
    
    public function getEmployeeById($id) {
        try {
            return $this->employeeRepositoryInterface->show($id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
    
    public function updateEmployee($data, $id) {
        try {
            return $this->employeeRepositoryInterface->update($data, $id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    public function deleteEmployee($id) {
        try {
            return $this->employeeRepositoryInterface->delete($id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    public function restoreEmployee($id) {
        try {
            return $this->employeeRepositoryInterface->restore($id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    public function applyLeave($data, $id){
        return  $this->employeeRepositoryInterface->applyLeave($data,$id);
    }
}