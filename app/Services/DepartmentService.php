<?php

namespace App\Services;

use App\Repository\Interface\DepartmentRepositoryInterface;

use function App\errorLogs;

class DepartmentService {

    protected $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function createDepartment($data) {
        try {
            return $this->departmentRepository->createDepartment($data);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    public function updateDepartment($data, $id) {
        try {
            return $this->departmentRepository->updateDepartment($data, $id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
}