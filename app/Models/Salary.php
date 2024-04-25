<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id', 
        'amount',
        'allowance',
        'effective_date',
        'leave_days'
    ];

    public function employee() {
        return $this->hasOne(Employee::class);
    }
}
