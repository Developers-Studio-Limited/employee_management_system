<?php

namespace App\Services;

use App\Repository\Interface\ForgetPasswordRepositoryInterface;

use function App\errorLogs;

class ForgetPasswordService {
    protected $repository;

    public function __construct(ForgetPasswordRepositoryInterface $forgetPasswordRepositoryInterface)
    {
        $this->repository = $forgetPasswordRepositoryInterface;
    }

    public function forgetPassword($user, $data){
        try {
            return $this->repository->forgetPassword($user, $data);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }
}