<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'earnings',
        'deductions',
        'gross_pay',
        'total_deductions',
        'net_pay',
        'pay_month',
        'pay_date'
    ];

    protected $casts = [
        'earnings' => 'array',
        'deductions' => 'array',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'pay_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
