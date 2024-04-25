<?php

namespace App\Providers;

use App\Repository\DepartmentRepository;
use App\Repository\EmployeeRepository;
use App\Repository\ForgetPasswordRepository;
use App\Repository\Interface\DepartmentRepositoryInterface;
use App\Repository\Interface\EmployeeRepositoryInterface;
use App\Repository\Interface\ForgetPasswordRepositoryInterface;
use App\Repository\Interface\leaveRepositoryInterface;
use App\Repository\Interface\SalaryRepositoryInterface;
use App\Repository\leaveRepository;
use App\Repository\SalaryRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(leaveRepositoryInterface::class, leaveRepository::class);
        $this->app->bind(ForgetPasswordRepositoryInterface::class, ForgetPasswordRepository::class);
        $this->app->bind(SalaryRepositoryInterface::class, SalaryRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
