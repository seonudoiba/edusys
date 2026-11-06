<?php
$e = function ($v) { return html_escape((string) $v); };
$statuses = Enquiries::STATUSES;
$flash = $this->session->flashdata('enquiry_msg');
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-inbox"></i> Enquiries <small>the platform website's contact and complaint forms</small></h1></section>
    <section class="content">
        <?php if ($flash) { ?><div class="alert alert-success"><?php echo $e($flash); ?></div><?php } ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <form method="get" action="<?php echo site_url('admin/enquiries'); ?>" style="display:flex;gap:8px;flex-wrap:wrap">
                    <select name="status" class="form-control input-sm" style="max-width:160px">
                        <option value="">All statuses</option>
                        <?php foreach ($statuses as $k => $st) { ?>
                            <option value="<?php echo $k; ?>" <?php echo $status === $k ? 'selected' : ''; ?>><?php echo $e($st[0]); ?> (<?php echo (int) $counts[$k]; ?>)</option>
                        <?php } ?>
                    </select>
                    <select name="kind" class="form-control input-sm" style="max-width:160px">
                        <option value="">All types</option>
                        <option value="quote" <?php echo $kind === 'quote' ? 'selected' : ''; ?>>Quote requests</option>
                        <option value="contact" <?php echo $kind === 'contact' ? 'selected' : ''; ?>>Contact only</option>
                        <option value="complaint" <?php echo $kind === 'complaint' ? 'selected' : ''; ?>>Complaints only</option>
                    </select>
                    <input type="search" name="q" class="form-control input-sm" style="max-width:260px" placeholder="Name, email or text" value="<?php echo $e($q); ?>">
                    <button class="btn btn-default btn-sm" type="submit"><i class="fa fa-filter"></i> Filter</button>
                </form>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Received</th><th>From</th><th>Subject</th><th>Type</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (!$rows) { ?><tr><td colspan="5" class="text-center text-muted" style="padding:30px">Nothing here.</td></tr><?php } ?>
                        <?php foreach ($rows as $r) { $st = $statuses[$r['status']]; ?>
                            <tr>
                                <td class="text-muted" style="white-space:nowrap"><?php echo $e(date('d M Y H:i', strtotime($r['created_at']))); ?></td>
                                <td><?php echo $e($r['name']); ?><div class="text-muted" style="font-size:11px"><?php echo $e($r['email']); ?></div></td>
                                <td><a href="<?php echo site_url('admin/enquiries/view/' . (int) $r['id']); ?>"><?php echo $e($r['subject'] ?: '(no subject)'); ?></a>
                                    <div class="text-muted" style="font-size:11px"><?php echo $e(mb_strimwidth($r['message'], 0, 120, '…')); ?></div></td>
                                <td><?php echo $r['kind'] === 'complaint' ? '<span class="label label-danger">Complaint</span>' : ($r['kind'] === 'quote' ? '<span class="label label-success">Quote</span>' : '<span class="label label-default">Contact</span>'); ?></td>
                                <td><span class="label <?php echo $st[1]; ?>"><?php echo $e($st[0]); ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
