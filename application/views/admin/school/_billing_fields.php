<?php
/*
 * The billing fields (bill_*), shared by Add school and the school page's "Bill this school".
 * $bands (price bands), $preview_url (POST target of Preview, new tab), $owner_email (the default
 * Send-to address; empty on Add school, where the form's email is used), $currency_symbol.
 * The live total uses the bands like the registration page.
 */
$bv = function ($k, $d = '') { return html_escape(set_value($k, $d)); };
$cs = isset($currency_symbol) ? $currency_symbol : '₦';
?>
<div class="bill-fields">
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="bill_students">Students</label><small class="req"> *</small>
                <input id="bill_students" name="bill_students" type="number" min="1" class="form-control" value="<?php echo $bv('bill_students'); ?>" placeholder="e.g. 300">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="bill_period">Period</label>
                <select id="bill_period" name="bill_period" class="form-control">
                    <option value="year" <?php echo set_select('bill_period', 'year', true); ?>>Yearly (3 terms)</option>
                    <option value="term" <?php echo set_select('bill_period', 'term'); ?>>One term</option>
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="bill_rate">Rate per student per term</label>
                <input id="bill_rate" name="bill_rate" type="number" min="0" step="0.01" class="form-control" value="<?php echo $bv('bill_rate'); ?>" placeholder="From the price band">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="bill_due">Due date</label>
                <input id="bill_due" name="bill_due" type="date" class="form-control" value="<?php echo $bv('bill_due', date('Y-m-d', strtotime('+7 days'))); ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="checkbox" style="margin-top:26px">
                <label><input type="checkbox" name="bill_setup" id="bill_setup" value="1" <?php echo set_checkbox('bill_setup', '1', true); ?>> Charge the setup fee</label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="bill_setup_fee">Setup fee</label>
                <input id="bill_setup_fee" name="bill_setup_fee" type="number" min="0" step="0.01" class="form-control" value="<?php echo $bv('bill_setup_fee'); ?>" placeholder="From the price band">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="bill_discount">Discount (amount)</label>
                <input id="bill_discount" name="bill_discount" type="number" min="0" step="0.01" class="form-control" value="<?php echo $bv('bill_discount'); ?>" placeholder="0">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>Total</label>
                <div id="bill_total" style="font-size:20px;font-weight:bold;line-height:34px">—</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="bill_notes">Note on the invoice <span class="text-muted" style="font-weight:normal">(optional, the school sees it)</span></label>
        <input id="bill_notes" name="bill_notes" type="text" maxlength="2000" class="form-control" value="<?php echo $bv('bill_notes'); ?>" placeholder="e.g. 2026/2027 session, as agreed on the phone">
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="checkbox">
                <label><input type="checkbox" name="bill_send" value="1" <?php echo set_checkbox('bill_send', '1', true); ?>> Email the invoice to the owner</label>
            </div>
            <input name="bill_email" id="bill_email" type="email" class="form-control" value="<?php echo $bv('bill_email', isset($owner_email) ? $owner_email : ''); ?>"
                   placeholder="<?php echo isset($owner_email) && $owner_email ? html_escape($owner_email) : 'The email address above'; ?>">
        </div>
        <div class="col-sm-6 text-right" style="padding-top:28px">
            <button type="submit" class="btn btn-default" formaction="<?php echo html_escape($preview_url); ?>" formtarget="_blank" formnovalidate>
                <i class="fa fa-eye"></i> Preview invoice
            </button>
        </div>
    </div>
</div>
<script>
(function () {
    var bands = <?php echo json_encode(isset($bands) ? $bands : array(), JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    var q = function (id) { return document.getElementById(id); };
    var cs = <?php echo json_encode($cs); ?>;
    function band(n) {
        for (var i = 0; i < bands.length; i++) {
            var b = bands[i];
            if (n >= +b.min_students && (b.max_students === null || n <= +b.max_students)) { return b; }
        }
        return null;
    }
    function money(v) { return cs + (Math.round(v * 100) / 100).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function total() {
        var n = parseInt(q('bill_students').value, 10) || 0, b = band(n), out = q('bill_total');
        var rate = q('bill_rate').value !== '' ? +q('bill_rate').value : (b && b.rate_per_term !== null ? +b.rate_per_term : null);
        q('bill_rate').placeholder = b && b.rate_per_term !== null ? 'Band: ' + money(+b.rate_per_term) : 'Enter the agreed rate';
        q('bill_setup_fee').placeholder = b ? 'Band: ' + money(+b.setup_fee) : 'From the price band';
        if (n < 1 || rate === null) { out.textContent = '—'; return; }
        var setup = q('bill_setup').checked ? (q('bill_setup_fee').value !== '' ? +q('bill_setup_fee').value : (b ? +b.setup_fee : 0)) : 0;
        var t = n * rate * (q('bill_period').value === 'year' ? 3 : 1) + setup - (+q('bill_discount').value || 0);
        out.textContent = money(Math.max(0, t));
    }
    ['bill_students', 'bill_period', 'bill_rate', 'bill_setup', 'bill_setup_fee', 'bill_discount'].forEach(function (id) {
        q(id).addEventListener('input', total); q(id).addEventListener('change', total);
    });
    total();
})();
</script>
