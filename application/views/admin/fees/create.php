<?php
$e = function ($v) { return html_escape((string) $v); };
$p = $plan ?: array('name' => '', 'term' => '', 'items' => array(), 'classes' => array(), 'amounts' => array());
$picked = array();
$new = array();
$due = array();
foreach ($p['items'] as $it) {
    if ($it['feetype_id'] !== null) {
        $picked[(int) $it['feetype_id']] = true;
    } else {
        $new[] = $it['name'];
    }
    $due[$it['key']] = $it['due_date'] ? date($this->customlib->getSchoolDateFormat(), strtotime($it['due_date'])) : '';
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-plus-circle"></i> Create fees <small>charge students for the <?php echo $e($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <ul class="fw-steps"><li class="on">1 Name</li><li class="on">2 Fee items</li><li class="on">3 Classes</li><li class="on">4 Amounts and due dates</li><li>5 Review</li></ul>
        <?php if (!empty($copied_from)) { ?>
            <div class="alert alert-info"><i class="fa fa-files-o"></i> Copied from <b><?php echo $e($copied_from); ?></b>: the same items, classes and amounts. Check the name, term and amounts, and set the due dates.</div>
        <?php } ?>
        <?php if ($errors) { ?>
            <div class="alert alert-danger"><b>Please fix the following:</b><ul style="margin:6px 0 0"><?php foreach ($errors as $err) { ?><li><?php echo $e($err); ?></li><?php } ?></ul></div>
        <?php } ?>

        <form method="post" action="<?php echo site_url('admin/fees/create'); ?>" id="cf">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="review">

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">1. What are these fees called, and for which term?</h3></div>
                <div class="box-body">
                    <input type="text" name="name" id="cf_name" class="form-control" style="max-width:420px" maxlength="150" required value="<?php echo $e($p['name']); ?>" placeholder="e.g. First Term fees">
                    <select name="term" id="cf_term" class="form-control" style="max-width:220px;margin-top:8px" aria-label="Term">
                        <option value="">Whole session (no term)</option>
                        <?php foreach (Fees_overview::TERMS as $k => $label) { ?><option value="<?php echo $k; ?>" <?php echo (isset($p['term']) ? $p['term'] : '') === $k ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
                    </select>
                    <p class="help-block">Each class gets its own set, named "<span id="cf_example"><?php echo $e($p['name'] ?: 'First Term fees'); ?></span> · JSS 1", so collection can be followed class by class.</p>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">2. Which fee items?</h3></div>
                <div class="box-body">
                    <?php if ($feetypes) { ?>
                        <div class="row">
                            <?php foreach ($feetypes as $f) { ?>
                                <div class="col-sm-4 col-md-3"><label class="checkbox-inline" style="margin:0 0 8px"><input type="checkbox" class="cf-item" name="feetype_ids[]" value="<?php echo (int) $f['id']; ?>" data-key="ft<?php echo (int) $f['id']; ?>" data-name="<?php echo $e($f['type']); ?>" <?php echo isset($picked[(int) $f['id']]) ? 'checked' : ''; ?>> <?php echo $e($f['type']); ?></label></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <p style="margin:10px 0 6px"><b>New items</b> <span class="fw-muted">(for example Tuition, PTA levy, Uniform)</span></p>
                    <div id="cf_new">
                        <?php foreach (array_pad($new, 1, '') as $i => $n) { ?>
                            <input type="text" class="form-control cf-new" name="new_items[<?php echo (int) $i; ?>]" data-key="new<?php echo (int) $i; ?>" value="<?php echo $e($n); ?>" placeholder="New fee item" style="max-width:320px;margin-bottom:6px">
                        <?php } ?>
                    </div>
                    <button type="button" class="btn btn-default btn-xs" id="cf_add_new"><i class="fa fa-plus"></i> Another new item</button>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">3. Which classes?</h3></div>
                <div class="box-body">
                    <?php if (!$classes) { ?>
                        <p class="fw-muted">No classes yet. Add them in Academics &gt; Class first.</p>
                    <?php } ?>
                    <div class="row">
                        <?php foreach ($classes as $cid => $c) { $on = isset($p['classes'][$cid]); ?>
                            <div class="col-sm-6 col-md-4" style="margin-bottom:10px">
                                <label style="margin:0"><input type="checkbox" class="cf-class" value="<?php echo (int) $cid; ?>" data-name="<?php echo $e($c['name']); ?>" <?php echo $on ? 'checked' : ''; ?>> <b><?php echo $e($c['name']); ?></b></label>
                                <div class="cf-sections" style="padding-left:20px">
                                    <?php foreach ($c['sections'] as $sid => $sname) { $son = $on ? in_array($sid, $p['classes'][$cid]) : true; ?>
                                        <label class="checkbox-inline" style="margin-right:6px"><input type="checkbox" name="sections[]" value="<?php echo (int) $cid . '-' . (int) $sid; ?>" data-class="<?php echo (int) $cid; ?>" <?php echo ($on && $son) ? 'checked' : ''; ?> <?php echo $on ? '' : 'disabled'; ?>> <?php echo $e($sname); ?></label>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">4. Amounts and due dates</h3>
                    <p class="fw-muted" style="margin:4px 0 0">Enter 0 where an item is not charged in a class. "Copy down" fills the column with the first class's amount.</p></div>
                <div class="box-body table-responsive" id="cf_amounts">
                    <p class="fw-muted" id="cf_amounts_empty">Choose fee items and classes above; the table appears here.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-eye"></i> Review before charging</button>
            <span class="fw-muted" style="margin-left:10px">Nothing is charged until you confirm on the next page.</span>
        </form>
    </section>
</div>
<script>
(function () {
    var amounts = <?php echo json_encode($p['amounts'], JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    var due = <?php echo json_encode($due, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    var esc = function (s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
    function items() {
        var out = [];
        document.querySelectorAll('.cf-item:checked').forEach(function (c) { out.push({ key: c.dataset.key, name: c.dataset.name }); });
        document.querySelectorAll('.cf-new').forEach(function (c) { if (c.value.trim()) { out.push({ key: c.dataset.key, name: c.value.trim() }); } });
        return out;
    }
    function classes() {
        var out = [];
        document.querySelectorAll('.cf-class:checked').forEach(function (c) { out.push({ id: c.value, name: c.dataset.name }); });
        return out;
    }
    function remember() {
        document.querySelectorAll('#cf_amounts input[data-c]').forEach(function (i) {
            amounts[i.dataset.c] = amounts[i.dataset.c] || {};
            amounts[i.dataset.c][i.dataset.k] = i.value;
        });
        document.querySelectorAll('#cf_amounts input[data-due]').forEach(function (i) { due[i.dataset.due] = i.value; });
    }
    function draw() {
        remember();
        var its = items(), cls = classes(), box = document.getElementById('cf_amounts');
        if (!its.length || !cls.length) {
            box.innerHTML = '<p class="fw-muted">Choose fee items and classes above; the table appears here.</p>';
            return;
        }
        var h = '<table class="table table-bordered fw-table"><thead><tr><th>Class</th>';
        its.forEach(function (it, j) { h += '<th>' + esc(it.name) + '<br><button type="button" class="btn btn-link btn-xs cf-copy" data-k="' + esc(it.key) + '" style="padding:0">Copy down</button></th>'; });
        h += '<th class="n">Per student</th></tr></thead><tbody>';
        cls.forEach(function (c) {
            h += '<tr><td><b>' + esc(c.name) + '</b></td>';
            its.forEach(function (it) {
                var v = amounts[c.id] && amounts[c.id][it.key] !== undefined ? amounts[c.id][it.key] : '';
                h += '<td><input type="text" inputmode="decimal" class="form-control input-sm cf-amt" name="amount[' + esc(c.id) + '][' + esc(it.key) + ']" data-c="' + esc(c.id) + '" data-k="' + esc(it.key) + '" value="' + esc(v) + '" style="min-width:110px"></td>';
            });
            h += '<td class="n cf-sum" data-c="' + esc(c.id) + '"></td></tr>';
        });
        h += '<tr class="active"><td><b>Due date</b></td>';
        its.forEach(function (it) { h += '<td><input type="text" class="form-control input-sm date" name="due[' + esc(it.key) + ']" data-due="' + esc(it.key) + '" value="' + esc(due[it.key] || '') + '" placeholder="optional" autocomplete="off"></td>'; });
        h += '<td></td></tr></tbody></table>';
        box.innerHTML = h;
        // .date fields get the school's datepicker from the layout (focusin).
        sums();
    }
    function sums() {
        document.querySelectorAll('.cf-sum').forEach(function (td) {
            var t = 0;
            document.querySelectorAll('.cf-amt[data-c="' + td.dataset.c + '"]').forEach(function (i) { t += parseFloat(String(i.value).replace(/,/g, '')) || 0; });
            td.textContent = t.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    }
    document.getElementById('cf').addEventListener('change', function (ev) {
        if (ev.target.classList.contains('cf-class')) {
            ev.target.closest('div').querySelectorAll('.cf-sections input').forEach(function (s) { s.disabled = !ev.target.checked; s.checked = ev.target.checked; });
        }
        if (ev.target.classList.contains('cf-item') || ev.target.classList.contains('cf-class') || ev.target.classList.contains('cf-new')) { draw(); }
    });
    document.getElementById('cf').addEventListener('input', function (ev) {
        if (ev.target.classList.contains('cf-amt')) { sums(); }
        if (ev.target.id === 'cf_name') { document.getElementById('cf_example').textContent = ev.target.value || 'First Term fees'; }
    });
    document.getElementById('cf').addEventListener('click', function (ev) {
        if (ev.target.classList.contains('cf-copy')) {
            var ins = document.querySelectorAll('.cf-amt[data-k="' + ev.target.dataset.k + '"]');
            if (ins.length) { ins.forEach(function (i) { i.value = ins[0].value; }); sums(); }
        }
    });
    var n = document.querySelectorAll('.cf-new').length;
    document.getElementById('cf_add_new').addEventListener('click', function () {
        var i = document.createElement('input');
        i.type = 'text'; i.className = 'form-control cf-new'; i.name = 'new_items[' + n + ']'; i.dataset.key = 'new' + n; i.placeholder = 'New fee item';
        i.style.maxWidth = '320px'; i.style.marginBottom = '6px';
        n++;
        document.getElementById('cf_new').appendChild(i);
        i.focus();
    });
    document.getElementById('cf').addEventListener('focusout', function (ev) { if (ev.target.classList.contains('cf-new')) { draw(); } });
    draw();
})();
</script>
