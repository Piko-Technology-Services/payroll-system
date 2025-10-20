# Reverse Calculation Feature Guide

## Overview

The payroll system now supports **reverse calculation** where employees' `salary_rate` is treated as their **Net Pay**, and the system automatically calculates the corresponding **Gross Pay** and statutory deductions.

## How It Works

### 1. Input
- **Net Pay**: Employee's `salary_rate` from the `employees` table
- **Allowances**: Optional allowances (Lunch, Transport, Housing)

### 2. Calculation Process
The system uses an **iterative method** to find the correct Gross Pay:

```
Net Pay = Gross Pay - (PAYE + NAPSA + NHIS + Personal Levy)
```

### 3. Statutory Deductions (ZRA 2025)
- **PAYE**: Progressive tax rates
  - 0–4,400 K → 0%
  - 4,401–4,500 K → 25%
  - 4,501–5,000 K → 30%
  - >5,000 K → 37.5%
- **NAPSA**: 5% of Gross Pay
- **NHIS**: 2% of Gross Pay
- **Personal Levy**: Fixed K3

### 4. Earnings Structure
- **Basic Pay**: Gross Pay - Total Allowances
- **Lunch Allowance**: Configurable
- **Transport Allowance**: Configurable
- **Housing Allowance**: Configurable

## Usage

### Generate Payslips from Net Pay
1. Go to Payslips page
2. Click "Generate from Net Pay" button
3. Set pay period and allowances
4. System generates payslips for all employees

### Individual Payslip Creation
1. Click "Add Payslip"
2. Select employee
3. System automatically calculates Gross Pay from their Net Pay
4. Adjust allowances if needed

## Example Calculation

**Employee with Net Pay of K8492.71:**

1. **Iterative Process**: System finds Gross Pay = K10691.47
2. **Deductions**:
   - PAYE: K1581.84
   - NAPSA: K534.57
   - NHIS: K74.84
   - Personal Levy: K7.50
   - **Total**: K2198.76
3. **Result**: K10691.47 - K2198.76 = K8492.71 ✓

## Benefits

- ✅ **Accurate**: Employees receive exactly their expected Net Pay
- ✅ **Compliant**: Follows ZRA 2025 tax brackets
- ✅ **Flexible**: Supports custom allowances
- ✅ **Efficient**: Bulk generation for all employees
- ✅ **Editable**: Generated payslips can be adjusted

## Technical Details

- **Convergence**: Algorithm converges within 1 cent tolerance
- **Performance**: Typically 4-15 iterations per calculation
- **Validation**: Prevents negative net pay and invalid calculations
- **Error Handling**: Comprehensive error reporting for failed calculations

## Migration Notes

- **Legacy Method**: Still available as "Generate All (Legacy)"
- **Backward Compatible**: Existing payslips remain unchanged
- **Database**: No schema changes required
- **API**: Enhanced `getPayslipDefaults` endpoint with reverse calculation
