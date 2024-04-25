<?php

namespace App\Services;

use App\Repository\Interface\leaveRepositoryInterface;

use function App\errorLogs;

class LeaveService {
    protected $leaveRepositoryInterface;
    public function __construct(leaveRepositoryInterface $leaveRepositoryInterface)
    {
        $this->leaveRepositoryInterface = $leaveRepositoryInterface;
    }

    public function getAllLeaves() {
        try {
            return $this->leaveRepositoryInterface->leaves();
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }

    public function leaveApproved($data, $id){
        try {
            return $this->leaveRepositoryInterface->approveLeave($data, $id);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__, $ex->getMessage());
        }
    }
}