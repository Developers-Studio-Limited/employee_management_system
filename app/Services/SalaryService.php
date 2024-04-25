<?php

namespace App\Services;

use App\Repository\Interface\SalaryRepositoryInterface;

use function App\errorLogs;

class  SalaryService {
    protected $salaryRepository;

    public function __construct(SalaryRepositoryInterface $salaryRepositoryInterface)
    {
        $this->salaryRepository = $salaryRepositoryInterface;
    }

    public function distributeSalary($data, $employeeId){
        try {
            return $this->salaryRepository->createSalary($data, $employeeId);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    public function updateSalary($data, $employeeId){
        try {
            return $this->salaryRepository->updateSalary($data, $employeeId);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
}