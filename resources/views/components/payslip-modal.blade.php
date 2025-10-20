<div class="modal fade" id="payslipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Payslip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="payslipForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Employee --}}
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" id="employeeSelect" required>
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fullnames }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pay Month / Date --}}
                        <div class="col-md-3">
                            <label class="form-label">Pay Month</label>
                            <input type="text" name="pay_month" class="form-control" id="payMonth" required placeholder="YYYY-MM">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pay Date</label>
                            <input type="date" name="pay_date" class="form-control" id="payDate" required>
                        </div>

                        <hr class="mt-3 mb-2">

                        {{-- Earnings --}}
                        <h6>Earnings</h6>
                        <div id="earningsFields" class="row g-3"></div>

                        <hr class="mt-3 mb-2">

                        {{-- Deductions --}}
                        <h6>Deductions</h6>
                        <div id="deductionsFields" class="row g-3"></div>

                        <hr class="mt-3 mb-2">

                        {{-- Totals --}}
                        <div class="col-md-4">
                            <label class="form-label">Gross Pay</label>
                            <input type="number" step="0.01" name="gross_pay" id="grossPay" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Deductions</label>
                            <input type="number" step="0.01" name="total_deductions" id="totalDeductions" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Net Pay</label>
                            <input type="number" step="0.01" name="net_pay" id="netPay" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="modalSubmit">Save Payslip</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Helper functions for calculations
function updateTotals() {
    const form = document.getElementById('payslipForm');
    const grossPayField = document.getElementById('grossPay');
    const totalDeductionsField = document.getElementById('totalDeductions');
    const netPayField = document.getElementById('netPay');
    
    // Only include enabled earnings fields
    const gross = Array.from(form.querySelectorAll('.earnings-field'))
        .filter(input => !input.disabled)
        .reduce((sum, i) => sum + parseFloat(i.value || 0), 0);
    
    // Only include enabled deductions fields
    const deductions = Array.from(form.querySelectorAll('.deductions-field'))
        .filter(input => !input.disabled)
        .reduce((sum, i) => sum + parseFloat(i.value || 0), 0);
    
    grossPayField.value = gross.toFixed(2);
    totalDeductionsField.value = deductions.toFixed(2);
    netPayField.value = (gross - deductions).toFixed(2);
}

function updateNetPay() {
    const grossPayField = document.getElementById('grossPay');
    const totalDeductionsField = document.getElementById('totalDeductions');
    const netPayField = document.getElementById('netPay');
    
    const gross = parseFloat(grossPayField.value || 0);
    const deductions = parseFloat(totalDeductionsField.value || 0);
    netPayField.value = (gross - deductions).toFixed(2);
}

window.openPayslipModal = async function(payslip = null) {
    const modalEl = document.getElementById('payslipModal');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('payslipForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('modalSubmit');

    const employeeSelect = document.getElementById('employeeSelect');
    const earningsContainer = document.getElementById('earningsFields');
    const deductionsContainer = document.getElementById('deductionsFields');
    const grossPayField = document.getElementById('grossPay');
    const totalDeductionsField = document.getElementById('totalDeductions');
    const netPayField = document.getElementById('netPay');
    const formMethod = document.getElementById('formMethod');

    // Reset form
    form.reset();
    earningsContainer.innerHTML = '';
    deductionsContainer.innerHTML = '';
    grossPayField.value = '';
    totalDeductionsField.value = '';
    netPayField.value = '';
    formMethod.value = 'POST';
    form.action = "{{ route('payslips.store') }}";
    modalTitle.textContent = 'Add Payslip';
    submitBtn.textContent = 'Save Payslip';

    if (payslip) {
        // Edit mode
        form.action = `/payslips/${payslip.id}`;
        formMethod.value = 'PUT';
        modalTitle.textContent = 'Edit Payslip';
        submitBtn.textContent = 'Save Changes';
        employeeSelect.value = payslip.employee_id;
        
        // Set existing payslip data
        document.getElementById('payDate').value = payslip.pay_date;
        document.getElementById('payMonth').value = payslip.pay_month;
    } else {
        // Add mode - Set current date and month as defaults
        const now = new Date();
        const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD format
        const currentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0'); // YYYY-MM format
        
        document.getElementById('payDate').value = currentDate;
        document.getElementById('payMonth').value = currentMonth;
    }

    // Fetch backend defaults only if employee is already selected (edit mode)
    const empId = employeeSelect.value;
    if (empId) {
        const response = await fetch(`/employees/${empId}/payslip-defaults`);
        if (response.ok) {
            const data = await response.json();
            
            if (!data.success) {
                alert('Error: ' + data.message);
                return;
            }

            // Render earnings with checkboxes
            earningsContainer.innerHTML = '';
            if (data.earningsData) {
                data.earningsData.forEach(earning => {
                    earningsContainer.innerHTML += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input earning-checkbox" type="checkbox" 
                                               id="earning_${earning.name.replace(/\s+/g, '_')}" 
                                               data-name="${earning.name}" checked>
                                        <label class="form-check-label fw-bold" for="earning_${earning.name.replace(/\s+/g, '_')}">
                                            ${earning.name}
                                        </label>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">K</span>
                                        <input type="number" step="0.01" 
                                               name="earnings[${earning.name}]" 
                                               class="form-control earnings-field" 
                                               value="${parseFloat(earning.amount || 0).toFixed(2)}"
                                               data-name="${earning.name}">
                                    </div>
                                    ${earning.description ? `<small class="text-muted">${earning.description}</small>` : ''}
                                </div>
                            </div>
                        </div>`;
                });
            }

            // Render deductions with checkboxes
            deductionsContainer.innerHTML = '';
            if (data.deductionsData) {
                data.deductionsData.forEach(deduction => {
                    const isStatutory = deduction.is_statutory ? ' <span class="badge bg-warning">Statutory</span>' : '';
                    deductionsContainer.innerHTML += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input deduction-checkbox" type="checkbox" 
                                               id="deduction_${deduction.name.replace(/\s+/g, '_')}" 
                                               data-name="${deduction.name}" checked>
                                        <label class="form-check-label fw-bold" for="deduction_${deduction.name.replace(/\s+/g, '_')}">
                                            ${deduction.name}${isStatutory}
                                        </label>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">K</span>
                                        <input type="number" step="0.01" 
                                               name="deductions[${deduction.name}]" 
                                               class="form-control deductions-field" 
                                               value="${parseFloat(deduction.amount || 0).toFixed(2)}"
                                               data-name="${deduction.name}">
                                    </div>
                                    ${deduction.description ? `<small class="text-muted">${deduction.description}</small>` : ''}
                                </div>
                            </div>
                        </div>`;
                });
            }

            grossPayField.value = Object.values(data.earnings).reduce((a,b) => a+b, 0).toFixed(2);
            totalDeductionsField.value = Object.values(data.deductions).reduce((a,b) => a+b, 0).toFixed(2);
            netPayField.value = data.net_pay.toFixed(2);

            // Add input listeners for live updates
            form.querySelectorAll('.earnings-field, .deductions-field').forEach(input => {
                input.addEventListener('input', () => {
                    updateTotals();
                });
            });

            // Add checkbox listeners
            form.querySelectorAll('.earning-checkbox, .deduction-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const fieldName = this.dataset.name;
                    const inputField = form.querySelector(`input[data-name="${fieldName}"]`);
                    
                    if (this.checked) {
                        inputField.disabled = false;
                        inputField.style.opacity = '1';
                    } else {
                        inputField.disabled = true;
                        inputField.style.opacity = '0.5';
                        inputField.value = '0.00';
                    }
                    updateTotals();
                });
            });

            // Add listeners for manual total field updates
            grossPayField.addEventListener('input', updateNetPay);
            totalDeductionsField.addEventListener('input', updateNetPay);
        }
    }

    // Update fields when employee changes
    employeeSelect.addEventListener('change', async function() {
        const empId = this.value;
        if (!empId) {
            // Clear fields when no employee selected
            earningsContainer.innerHTML = '';
            deductionsContainer.innerHTML = '';
            grossPayField.value = '';
            totalDeductionsField.value = '';
            netPayField.value = '';
            return;
        }

        const response = await fetch(`/employees/${empId}/payslip-defaults`);
        if (response.ok) {
            const data = await response.json();

            if (!data.success) {
                alert('Error: ' + data.message);
                return;
            }

            // Render earnings with checkboxes
            earningsContainer.innerHTML = '';
            if (data.earningsData) {
                data.earningsData.forEach(earning => {
                    earningsContainer.innerHTML += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input earning-checkbox" type="checkbox" 
                                               id="earning_${earning.name.replace(/\s+/g, '_')}" 
                                               data-name="${earning.name}" checked>
                                        <label class="form-check-label fw-bold" for="earning_${earning.name.replace(/\s+/g, '_')}">
                                            ${earning.name}
                                        </label>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">K</span>
                                        <input type="number" step="0.01" 
                                               name="earnings[${earning.name}]" 
                                               class="form-control earnings-field" 
                                               value="${parseFloat(earning.amount || 0).toFixed(2)}"
                                               data-name="${earning.name}">
                                    </div>
                                    ${earning.description ? `<small class="text-muted">${earning.description}</small>` : ''}
                                </div>
                            </div>
                        </div>`;
                });
            }

            // Render deductions with checkboxes
            deductionsContainer.innerHTML = '';
            if (data.deductionsData) {
                data.deductionsData.forEach(deduction => {
                    const isStatutory = deduction.is_statutory ? ' <span class="badge bg-warning">Statutory</span>' : '';
                    deductionsContainer.innerHTML += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input deduction-checkbox" type="checkbox" 
                                               id="deduction_${deduction.name.replace(/\s+/g, '_')}" 
                                               data-name="${deduction.name}" checked>
                                        <label class="form-check-label fw-bold" for="deduction_${deduction.name.replace(/\s+/g, '_')}">
                                            ${deduction.name}${isStatutory}
                                        </label>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">K</span>
                                        <input type="number" step="0.01" 
                                               name="deductions[${deduction.name}]" 
                                               class="form-control deductions-field" 
                                               value="${parseFloat(deduction.amount || 0).toFixed(2)}"
                                               data-name="${deduction.name}">
                                    </div>
                                    ${deduction.description ? `<small class="text-muted">${deduction.description}</small>` : ''}
                                </div>
                            </div>
                        </div>`;
                });
            }

            grossPayField.value = Object.values(data.earnings).reduce((a,b) => a+b, 0).toFixed(2);
            totalDeductionsField.value = Object.values(data.deductions).reduce((a,b) => a+b, 0).toFixed(2);
            netPayField.value = data.net_pay.toFixed(2);

            // Add input listeners again
            form.querySelectorAll('.earnings-field, .deductions-field').forEach(input => {
                input.addEventListener('input', () => {
                    updateTotals();
                });
            });

            // Add checkbox listeners
            form.querySelectorAll('.earning-checkbox, .deduction-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const fieldName = this.dataset.name;
                    const inputField = form.querySelector(`input[data-name="${fieldName}"]`);
                    
                    if (this.checked) {
                        inputField.disabled = false;
                        inputField.style.opacity = '1';
                    } else {
                        inputField.disabled = true;
                        inputField.style.opacity = '0.5';
                        inputField.value = '0.00';
                    }
                    updateTotals();
                });
            });

            // Add listeners for manual total field updates
            grossPayField.addEventListener('input', updateNetPay);
            totalDeductionsField.addEventListener('input', updateNetPay);
        }
    });

    // Show modal
    modal.show();
}

// Handle form submission to exclude disabled fields
document.getElementById('payslipForm').addEventListener('submit', function(e) {
    // Remove disabled fields from form data before submission
    const disabledFields = this.querySelectorAll('input:disabled');
    disabledFields.forEach(field => {
        if (field.name && (field.name.startsWith('earnings[') || field.name.startsWith('deductions['))) {
            field.remove();
        }
    });
});
</script>
