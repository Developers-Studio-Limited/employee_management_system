<?php
namespace App\Repository\Interface;

interface SalaryRepositoryInterface {
    public function createSalary($data, $employeeId);
    public function updateSalary($data, $employeeId);
}