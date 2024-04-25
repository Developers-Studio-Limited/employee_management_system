<?php

namespace App\Repository\Interface;

interface leaveRepositoryInterface {
    public function leaves();
    public function approveLeave($data, $id);
}