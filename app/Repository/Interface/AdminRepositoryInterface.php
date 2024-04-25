<?php

namespace App\Repository\Interface;

interface AdminRepositoryInterface {
    public function index();
    public function create($data);
    public function show($id);
    public function update($data, $id);
    public function delete($id);
    public function restore($id);
    public function applyLeave($data, $id);
}