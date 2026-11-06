<?php $e = function ($v) { return html_escape((string) $v); }; ?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-tags"></i> Pricing bands <small>what schools pay, by size</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="box box-primary">
            <form method="post" action="<?php echo site_url('admin/billing/save_bands'); ?>">
                <?php echo $token; ?>
                <div class="box-body table-responsive">
                    <p class="text-muted">The website and registration use these prices straight away. Schools that have already paid keep their price until they renew; a yearly subscription is three terms.</p>
                    <table class="table">
                        <thead><tr><th>Students</th><th>Per student, per term (NGN)</th><th>One-time implementation and training (NGN)</th></tr></thead>
                        <tbody>
                            <?php foreach ($bands as $b) { $id = (int) $b['id']; ?>
                                <tr>
                                    <td style="vertical-align:middle"><b><?php echo number_format((int) $b['min_students']) . ($b['max_students'] === null ? '+' : ' &ndash; ' . number_format((int) $b['max_students'])); ?></b></td>
                                    <td>
                                        <?php if ($b['rate_per_term'] === null) { ?>
                                            <span class="text-muted">Custom quote (sent from Website Enquiries)</span>
                                        <?php } else { ?>
                                            <input class="form-control" style="max-width:180px" type="number" min="1" step="0.01" required name="rate_<?php echo $id; ?>" value="<?php echo $e((float) $b['rate_per_term']); ?>" aria-label="Rate per term">
                                        <?php } ?>
                                    </td>
                                    <td><input class="form-control" style="max-width:180px" type="number" min="0" step="0.01" required name="setup_<?php echo $id; ?>" value="<?php echo $e((float) $b['setup_fee']); ?>" aria-label="Setup fee"></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($this->rbac->hasPrivilege('subscription_list', 'can_edit')) { ?>
                    <div class="box-footer"><button class="btn btn-primary" type="submit">Save prices</button></div>
                <?php } ?>
            </form>
        </div>
    </section>
</div>
