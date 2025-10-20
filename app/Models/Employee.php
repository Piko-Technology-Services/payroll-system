<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullnames',
        'employee_id',
        'date_engaged',
        'salary_rate',
        'company',
        'branch',
        'department',
        'position',
        'pay_method',
        'bank_acc_number',
        'nrc_number',
        'ssn',
        'nhi_no',
        'leave_days',
        'tpin'
    ];

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }
}
