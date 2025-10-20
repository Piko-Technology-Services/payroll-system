import './bootstrap';

$(document).ready(function() {
    $('#employeeSelect').on('change', function() {
        var employeeId = $(this).val();
        if(employeeId) {
            $.ajax({
                url: '/api/employees/' + employeeId + '/statutory',
                method: 'GET',
                success: function(data) {
                    // Fill earnings fields according to statutory rules
                    Object.keys(data.earnings).forEach(function(key) {
                        var field = $("input[name='earnings["+key+"]']");
                        if(field.length) field.val(data.earnings[key]);
                    });
                    // Fill deductions fields according to statutory rules
                    Object.keys(data.deductions).forEach(function(key) {
                        var field = $("input[name='deductions["+key+"]']");
                        if(field.length) field.val(data.deductions[key]);
                    });
                    calculateNetPay();
                }
            });
        }
    });
});
