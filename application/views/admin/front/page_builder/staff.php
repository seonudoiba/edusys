<?php $e = function ($v) { return html_escape((string) $v); }; ?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-users"></i> Staff on the website <small>who the public Staff directory widget lists</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <div class="box box-primary">
            <form method="post" action="<?php echo site_url('admin/front/page_builder/staff'); ?>">
                <?php echo $token; ?>
                <div class="box-body">
                    <p class="text-muted">Visitors see the name, role and photo of the people ticked here. Nobody is listed until you tick them. It is a good idea to ask staff first.</p>
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead><tr><th style="width:90px">Show</th><th>Name</th><th>Role</th></tr></thead>
                            <tbody>
                                <?php if (!$staff) { ?><tr><td colspan="3" class="text-muted text-center">No active staff.</td></tr><?php } ?>
                                <?php foreach ($staff as $s) { ?>
                                    <tr>
                                        <td><input type="checkbox" name="show[]" value="<?php echo (int) $s['id']; ?>" aria-label="Show <?php echo $e($s['name']); ?>" <?php echo (int) $s['show_on_website'] === 1 ? 'checked' : ''; ?>></td>
                                        <td><?php echo $e(trim($s['name'] . ' ' . $s['surname'])); ?></td>
                                        <td class="text-muted"><?php echo $e($s['designation']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer"><button class="btn btn-primary" type="submit">Save</button> <a class="btn btn-link" href="javascript:history.back()">Back</a></div>
            </form>
        </div>
    </section>
</div>
