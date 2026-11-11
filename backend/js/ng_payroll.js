/*
 * Nigerian statutory deductions for the payroll form (admin/payroll/create and edit).
 *
 *   Pension (employee)  8% of basic + housing + transport (Pension Reform Act 2014); housing and
 *                       transport are the allowance rows whose type mentions housing, rent or transport
 *   NHF                 2.5% of basic (National Housing Fund)
 *   PAYE                Nigeria Tax Act 2025 bands on the year's pay after pension and NHF:
 *                       first 800,000 at 0%, next 2,200,000 at 15%, next 9,000,000 at 18%,
 *                       next 13,000,000 at 21%, next 25,000,000 at 23%, above 50,000,000 at 25%
 *
 * Pension and NHF go into deduction rows (replacing rows of the same name), PAYE into the Tax field;
 * then the form's own Calculate runs. Every figure stays editable before saving.
 */
(function () {
    var BANDS = [[800000, 0], [2200000, 0.15], [9000000, 0.18], [13000000, 0.21], [25000000, 0.23], [Infinity, 0.25]];
    var num = function (v) { var n = parseFloat(String(v).replace(/,/g, '')); return isNaN(n) ? 0 : n; };
    var round2 = function (n) { return Math.round(n * 100) / 100; };

    function annualPaye(taxable) {
        var tax = 0, left = Math.max(0, taxable);
        for (var i = 0; i < BANDS.length && left > 0; i++) {
            var part = Math.min(left, BANDS[i][0]);
            tax += part * BANDS[i][1];
            left -= part;
        }
        return tax;
    }

    function setDeduction(label, amount) {
        var types = document.getElementsByName('deduction_type[]');
        var amounts = document.getElementsByName('deduction_amount[]');
        for (var i = 0; i < types.length; i++) {
            if (types[i].value === label) { amounts[i].value = amount.toFixed(2); return; }
        }
        for (var j = 0; j < types.length; j++) { // an empty row first
            if (types[j].value === '' && num(amounts[j].value) === 0) { types[j].value = label; amounts[j].value = amount.toFixed(2); return; }
        }
        if (typeof window.add_more_deduction === 'function') {
            window.add_more_deduction();
            types = document.getElementsByName('deduction_type[]');
            amounts = document.getElementsByName('deduction_amount[]');
            types[types.length - 1].value = label;
            amounts[amounts.length - 1].value = amount.toFixed(2);
        }
    }

    window.ngPayrollDeductions = function () {
        var basic = num(document.getElementById('basic').value);
        var types = document.getElementsByName('allowance_type[]');
        var amounts = document.getElementsByName('allowance_amount[]');
        var allowances = 0, housingTransport = 0;
        for (var i = 0; i < amounts.length; i++) {
            var a = num(amounts[i].value);
            allowances += a;
            if (/hous|rent|transport/i.test(types[i].value)) { housingTransport += a; }
        }
        var pension = round2(0.08 * (basic + housingTransport));
        var nhf = round2(0.025 * basic);
        var yearly = 12 * (basic + allowances) - 12 * (pension + nhf);
        var paye = round2(annualPaye(yearly) / 12);
        setDeduction('Pension (8%)', pension);
        setDeduction('NHF (2.5%)', nhf);
        document.getElementById('tax').value = paye.toFixed(2);
        if (typeof window.add_allowance === 'function') { window.add_allowance(); }
        var note = document.getElementById('ng_payroll_note');
        if (note) {
            note.textContent = 'Pension ' + pension.toLocaleString('en-NG') + ', NHF ' + nhf.toLocaleString('en-NG') + ', PAYE '
                + paye.toLocaleString('en-NG') + ' a month. Check the figures before saving.';
        }
    };
    window.ngPayrollAnnualPaye = annualPaye; // for checking
})();
