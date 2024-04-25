<?php

namespace App\Repository\Interface;

interface ForgetPasswordRepositoryInterface {
    public function forgetPassword($user, $data);
}