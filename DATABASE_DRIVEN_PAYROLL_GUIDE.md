# Database-Driven Payroll System Guide

## Overview

The payroll system has been refactored to implement **standard gross-to-net calculations** with **database-driven default earnings and deductions**. This makes the system more professional, maintainable, and flexible.

## Key Features

### ✅ **Standard Payroll Calculations**
- **Gross Pay = Basic Pay + Default Allowances + Optional Earnings**
- **Net Pay = Gross Pay - Total Deductions**
- **Professional approach** following industry standards

### ✅ **Database-Driven Defaults**
- All default earnings and deductions stored in database
- **Editable via web interface** - no code changes needed
- **Real-time updates** affect all new payslip calculations
- **Flexible configuration** with fixed amounts or percentages

### ✅ **ZRA 2025 Compliance**
- **PAYE**: Progressive tax rates (0%, 25%, 30%, 37.5%)
- **NAPSA**: 5% of gross pay
- **NHIS**: 2% of gross pay
- **Personal Levy**: Fixed K3

## Database Structure

### **Default Earnings Table**
```sql
- id (primary key)
- name (unique) - e.g., "Basic Pay", "Lunch Allowance"
- amount (decimal) - fixed amount or percentage
- type (enum) - "fixed" or "percentage"
- description (text) - optional description
- is_active (boolean) - enable/disable
- sort_order (integer) - display order
```

### **Default Deductions Table**
```sql
- id (primary key)
- name (unique) - e.g., "PAYE", "NAPSA", "NHIS"
- amount (decimal) - fixed amount or percentage
- type (enum) - "fixed" or "percentage"
- description (text) - optional description
- is_active (boolean) - enable/disable
- is_statutory (boolean) - statutory vs regular deduction
- sort_order (integer) - display order
```

## Default Data

### **Default Earnings**
1. **Basic Pay** (System) - Employee's base salary
2. **Lunch Allowance** - Monthly lunch allowance
3. **Transport Allowance** - Monthly transport allowance
4. **Housing Allowance** - Monthly housing allowance
5. **Overtime** - Overtime pay
6. **Bonus** - Performance bonus

### **Default Deductions**
1. **PAYE** (Statutory) - Calculated based on ZRA 2025 rates
2. **NAPSA** (Statutory) - 5% of gross pay
3. **NHIS** (Statutory) - 2% of gross pay
4. **Personal Levy** (Statutory) - Fixed K3
5. **Loan Deduction** - Staff loan repayment
6. **Advance Deduction** - Salary advance repayment
7. **Union Dues** - Trade union membership dues

## Usage Instructions

### **1. Manage Default Earnings**
- Go to Payslips page
- Click **"Default Earnings"** button
- Add, edit, or deactivate earnings
- Changes affect all new payslips immediately

### **2. Manage Default Deductions**
- Go to Payslips page
- Click **"Default Deductions"** button
- Add, edit, or deactivate deductions
- Mark deductions as statutory or regular

### **3. Generate Payslips**
- **Standard Method**: Uses employee's salary_rate as Basic Pay
- **Reverse Method**: Uses employee's salary_rate as Net Pay
- Both methods use database-driven defaults

### **4. Individual Payslips**
- Select employee → system loads database defaults
- Add optional earnings/deductions as needed
- All calculations use current database values

## Calculation Examples

### **Example 1: Standard Calculation**
**Employee with Basic Pay K5,000:**

1. **Earnings** (from database defaults):
   - Basic Pay: K5,000
   - Lunch Allowance: K200 (fixed)
   - Transport Allowance: K300 (fixed)
   - Housing Allowance: K500 (fixed)
   - **Gross Pay**: K6,000

2. **Deductions** (from database defaults):
   - PAYE: K193.92 (calculated)
   - NAPSA: K300 (5% of K6,000)
   - NHIS: K120 (2% of K6,000)
   - Personal Levy: K3 (fixed)
   - **Total Deductions**: K616.92

3. **Net Pay**: K6,000 - K616.92 = **K5,383.08**

### **Example 2: Percentage-Based Allowance**
If Lunch Allowance is set to 5% in database:
- Basic Pay: K5,000
- Lunch Allowance: K250 (5% of K5,000)
- **Gross Pay**: K5,250

## API Endpoints

### **Default Earnings**
- `GET /default-earnings` - List all earnings
- `POST /default-earnings` - Create new earning
- `PUT /default-earnings/{id}` - Update earning
- `DELETE /default-earnings/{id}` - Delete earning
- `PATCH /default-earnings/{id}/toggle-status` - Toggle active status

### **Default Deductions**
- `GET /default-deductions` - List all deductions
- `POST /default-deductions` - Create new deduction
- `PUT /default-deductions/{id}` - Update deduction
- `DELETE /default-deductions/{id}` - Delete deduction
- `PATCH /default-deductions/{id}/toggle-status` - Toggle active status

## Migration Instructions

### **1. Run Migrations**
```bash
php artisan migrate
```

### **2. Seed Default Data**
```bash
php artisan db:seed --class=DefaultEarningsAndDeductionsSeeder
```

### **3. Verify Setup**
- Check that default earnings and deductions are created
- Test payslip generation with new system
- Verify calculations match expected results

## Benefits

### ✅ **Professional Standards**
- Follows industry-standard gross-to-net calculations
- ZRA 2025 compliant tax calculations
- Proper separation of statutory vs regular deductions

### ✅ **Maintainability**
- No hard-coded values in source code
- Database-driven configuration
- Easy to add new earnings/deductions

### ✅ **Flexibility**
- Support for fixed amounts and percentages
- Enable/disable earnings and deductions
- Customizable sort order

### ✅ **User-Friendly**
- Web interface for managing defaults
- Real-time updates
- Clear distinction between statutory and regular items

### ✅ **Backward Compatibility**
- Legacy methods still available
- Existing payslips remain unchanged
- Gradual migration possible

## Technical Implementation

### **PayslipCalculator Service**
- Uses `DefaultEarning::active()->ordered()->get()`
- Uses `DefaultDeduction::active()->ordered()->get()`
- Handles statutory deductions with special calculations
- Supports both fixed and percentage-based amounts

### **Models**
- `DefaultEarning` - Manages default earnings
- `DefaultDeduction` - Manages default deductions
- Both include scopes for active items and ordering

### **Controllers**
- `DefaultEarningController` - CRUD operations for earnings
- `DefaultDeductionController` - CRUD operations for deductions
- Full validation and error handling

### **Views**
- Professional management interfaces
- Modal-based editing
- Status indicators and badges
- Responsive design

## Security & Validation

- **Input validation** for all numeric fields
- **Unique constraints** on earning/deduction names
- **CSRF protection** on all forms
- **Authorization checks** (can be added as needed)
- **Data integrity** with foreign key constraints

## Performance Considerations

- **Database indexing** on frequently queried fields
- **Eager loading** for related data
- **Caching** can be added for frequently accessed defaults
- **Optimized queries** with scopes and relationships

The refactored system provides a professional, maintainable, and flexible payroll solution that follows industry standards while being easy to configure and use.
