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
                            <input type="text" name="pay_month" class="form-control" id="payMonth" required>
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
                            <input type="number" step="0.01" name="gross_pay" id="grossPay" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Deductions</label>
                            <input type="number" step="0.01" name="total_deductions" id="totalDeductions" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Net Pay</label>
                            <input type="number" step="0.01" name="net_pay" id="netPay" class="form-control" readonly>
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
    }

    // Fetch backend defaults
    const empId = employeeSelect.value;
    if (empId) {
        const response = await fetch(`/employees/${empId}/payslip-defaults`);
        if (response.ok) {
            const data = await response.json();
            
            if (!data.success) {
                alert('Error: ' + data.message);
                return;
            }

            // Render earnings
            earningsContainer.innerHTML = '';
            for (const [key, value] of Object.entries(data.earnings)) {
                earningsContainer.innerHTML += `
                    <div class="col-md-4 mb-2">
                        <label class="form-label">${key}</label>
                        <input type="number" step="0.01" name="earnings[${key}]" class="form-control earnings-field" value="${value.toFixed(2)}">
                    </div>`;
            }

            // Render deductions
            deductionsContainer.innerHTML = '';
            for (const [key, value] of Object.entries(data.deductions)) {
                deductionsContainer.innerHTML += `
                    <div class="col-md-4 mb-2">
                        <label class="form-label">${key}</label>
                        <input type="number" step="0.01" name="deductions[${key}]" class="form-control deductions-field" value="${value.toFixed(2)}">
                    </div>`;
            }

            grossPayField.value = Object.values(data.earnings).reduce((a,b) => a+b, 0).toFixed(2);
            totalDeductionsField.value = Object.values(data.deductions).reduce((a,b) => a+b, 0).toFixed(2);
            netPayField.value = data.net_pay.toFixed(2);

            // Add input listeners for live updates
            form.querySelectorAll('.earnings-field, .deductions-field').forEach(input => {
                input.addEventListener('input', () => {
                    const gross = Array.from(form.querySelectorAll('.earnings-field'))
                        .reduce((sum, i) => sum + parseFloat(i.value || 0), 0);
                    const deductions = Array.from(form.querySelectorAll('.deductions-field'))
                        .reduce((sum, i) => sum + parseFloat(i.value || 0), 0);
                    grossPayField.value = gross.toFixed(2);
                    totalDeductionsField.value = deductions.toFixed(2);
                    netPayField.value = (gross - deductions).toFixed(2);
                });
            });
        }
    }

    // Update fields when employee changes
    employeeSelect.addEventListener('change', async function() {
        const empId = this.value;
        if (!empId) return;

        const response = await fetch(`/employees/${empId}/payslip-defaults`);
        if (response.ok) {
            const data = await response.json();

            earningsContainer.innerHTML = '';
            for (const [key, value] of Object.entries(data.earnings)) {
                earningsContainer.innerHTML += `
                    <div class="col-md-4 mb-2">
                        <label class="form-label">${key}</label>
                        <input type="number" step="0.01" name="earnings[${key}]" class="form-control earnings-field" value="${value.toFixed(2)}">
                    </div>`;
            }

            deductionsContainer.innerHTML = '';
            for (const [key, value] of Object.entries(data.deductions)) {
                deductionsContainer.innerHTML += `
                    <div class="col-md-4 mb-2">
                        <label class="form-label">${key}</label>
                        <input type="number" step="0.01" name="deductions[${key}]" class="form-control deductions-field" value="${value.toFixed(2)}">
                    </div>`;
            }

            grossPayField.value = Object.values(data.earnings).reduce((a,b) => a+b, 0).toFixed(2);
            totalDeductionsField.value = Object.values(data.deductions).reduce((a,b) => a+b, 0).toFixed(2);
            netPayField.value = data.net_pay.toFixed(2);

            // Add input listeners again
            form.querySelectorAll('.earnings-field, .deductions-field').forEach(input => {
                input.addEventListener('input', () => {
                    const gross = Array.from(form.querySelectorAll('.earnings-field'))
                        .reduce((sum, i) => sum + parseFloat(i.value || 0), 0);
                    const deductions = Array.from(form.querySelectorAll('.deductions-field'))
                        .reduce((sum, i) => sum + parseFloat(i.value || 0), 0);
                    grossPayField.value = gross.toFixed(2);
                    totalDeductionsField.value = deductions.toFixed(2);
                    netPayField.value = (gross - deductions).toFixed(2);
                });
            });
        }
    });

    // Show modal
    modal.show();
}
</script>
