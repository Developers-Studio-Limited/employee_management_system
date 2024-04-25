<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;
    protected $table = 'error_logs'; // Adjust if your table name is different
    protected $fillable = ['method_name', 'line_no', 'error', 'api_request_id']; // Define fillable attributes
}
