<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-bell-o"></i> Reminders and reports <small><?php echo html_escape($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <h4 style="margin:4px 0 10px">Follow up</h4>
        <div class="fw-cards">
            <div class="fw-card">
                <h4><i class="fa fa-bell"></i> Automatic reminders</h4>
                <p>Email and SMS reminders sent a set number of days before and after fees are due.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/feereminder/setting'); ?>">Set up reminders</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-whatsapp"></i> Remind on WhatsApp</h4>
                <p>Open a class in the Overview and tap WhatsApp next to a student: the message to the guardian is typed for you.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees?status=owing'); ?>">Classes with balances</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-share"></i> Carry balances forward</h4>
                <p>At the start of a new session, bring each student's unpaid balance from the last session.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/carryforward'); ?>">Carry forward</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-tag"></i> Discounts</h4>
                <p>Sibling, staff-child or scholarship discounts, and the students they apply to.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/discounts'); ?>">Give a discount</a>
            </div>
        </div>
        <h4 style="margin:22px 0 10px">Reports</h4>
        <div class="fw-cards">
            <div class="fw-card">
                <h4><i class="fa fa-line-chart"></i> Finance reports</h4>
                <p>Balances, statements, daily and online collections, fee groups, income and expenses, with Excel and PDF export.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('financereports/finance'); ?>">Open finance reports</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-cogs"></i> Fee setup, item by item</h4>
                <p>The detailed screens behind Create fees, for changing an amount, a due date or a fine on one item.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/feemaster'); ?>">Fees Master</a>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/feegroup'); ?>">Groups</a>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/feetype'); ?>">Items</a>
            </div>
        </div>
    </section>
</div>
