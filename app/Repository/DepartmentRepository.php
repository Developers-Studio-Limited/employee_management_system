<?php 

namespace App\Repository;

use App\Models\Department;
use App\Repository\Interface\DepartmentRepositoryInterface;

use function App\errorLogs;

class  DepartmentRepository implements DepartmentRepositoryInterface {
   
    /**
     * Create a new department.
     *
     * @param array $data Data for creating a new department.
     * @return \App\Models\Department|null Newly created department instance if successful, null otherwise.
     */
    public function createDepartment($data) {
        try {
            return Department::create($data);
        } catch (\Exception $ex) {
            errorLogs(__METHOD__,__LINE__,$ex->getMessage() );
        }
    }

    /**
     * Update an existing department.
     *
     * @param array $data Data for updating the department.
     * @param int $id ID of the department to be updated.
     * @return \App\Models\Department|null Updated department instance if successful, null otherwise.
     */
    public function updateDepartment($data, $id) {
        try {
            $department = Department::findOrfail($id);
            $department->update($data);
            return $department;
        } catch(\Exception $ex) {
            errorLogs(__METHOD__,__LINE__,$ex->getMessage() );
        }
    }
}