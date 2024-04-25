<?php

namespace App\Repository\Interface;

interface EmployeeRepositoryInterface {
    public function applyLeave($data, $id);
    public function empSalary($data);
}
