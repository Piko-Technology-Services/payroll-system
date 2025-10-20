<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\EarningRule;
use App\Models\DeductionRule;

class PayslipModal extends Component
{
    public $payslip;
    public $employees;
    public $type; // 'add' or 'edit'
    public $earningRules;
    public $deductionRules;

    public function __construct($employees, $type = 'add', $payslip = null)
    {
        $this->employees = $employees;
        $this->type = $type;
        $this->payslip = $payslip;
        $this->earningRules = EarningRule::all();
        $this->deductionRules = DeductionRule::all();
    }

    public function render()
    {
        return view('components.payslip-modal');
    }
}
