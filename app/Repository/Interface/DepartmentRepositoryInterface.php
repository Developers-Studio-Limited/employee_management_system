<?php 

namespace App\Repository\Interface;

interface DepartmentRepositoryInterface {
    public function createDepartment($data);
    public function updateDepartment($data, $id);
}