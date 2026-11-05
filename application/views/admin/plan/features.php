<?php
$e = function ($v) { return html_escape((string) $v); };
$can_add = $this->rbac->hasPrivilege('plan_list', 'can_add');
$can_edit = $this->rbac->hasPrivilege('plan_list', 'can_edit');
$can_delete = $this->rbac->hasPrivilege('plan_list', 'can_delete');
?>
<style>
    .ft-row form { margin: 0; }
    .ft-row td { vertical-align: middle !important; }
    .ft-inline { display: flex; gap: 6px; align-items: center; }
    .ft-inline .ft-order { width: 80px; }
    .ft-plans { font-size: 12px; color: #777; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><a href="<?php echo site_url('admin/plan'); ?>" class="text-muted"><i class="fa fa-arrow-left"></i></a>
            Plan features <small>the lines listed on each plan on the sign-up page</small></h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <?php if ($can_add) { ?>
            <div class="box box-primary">
                <div class="box-body">
                    <form method="post" action="<?php echo site_url('admin/plan_type/save'); ?>" class="ft-inline">
                        <?php echo $token; ?>
                        <input name="name" class="form-control" maxlength="255" required placeholder="New feature, e.g. Unlimited students">
                        <input name="sort_order" type="number" min="0" max="99999" class="form-control ft-order" value="<?php echo (int) $next_order; ?>" title="Order (lower comes first)">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> Add</button>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="box box-default">
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th style="width:55%">Feature and order</th><th>Listed on</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        <?php if (!$features) { ?><tr><td colspan="4" class="text-muted text-center" style="padding:30px">No features yet.</td></tr><?php } ?>
                        <?php foreach ($features as $f) { $shown = (int) $f['status'] === 1; ?>
                            <tr class="ft-row">
                                <td>
                                    <?php if ($can_edit) { ?>
                                        <form method="post" action="<?php echo site_url('admin/plan_type/save/' . (int) $f['id']); ?>" class="ft-inline">
                                            <?php echo $token; ?>
                                            <input name="name" class="form-control" maxlength="255" required value="<?php echo $e($f['name']); ?>">
                                            <input name="sort_order" type="number" min="0" max="99999" class="form-control ft-order" value="<?php echo (int) $f['sort_order']; ?>" title="Order">
                                            <button class="btn btn-default" type="submit" title="Save"><i class="fa fa-save"></i></button>
                                        </form>
                                    <?php } else { echo $e($f['name']); } ?>
                                </td>
                                <td class="ft-plans"><?php echo $f['plans'] ? $e(implode(', ', $f['plans'])) : 'No plan'; ?></td>
                                <td><span class="label <?php echo $shown ? 'label-success' : 'label-default'; ?>"><?php echo $shown ? 'Shown' : 'Hidden'; ?></span></td>
                                <td class="text-right" style="white-space:nowrap">
                                    <?php if ($can_edit) { ?>
                                        <form method="post" action="<?php echo site_url('admin/plan_type/toggle/' . (int) $f['id']); ?>" style="display:inline"><?php echo $token; ?>
                                            <button class="btn btn-sm btn-default" type="submit"><?php echo $shown ? 'Hide' : 'Show'; ?></button>
                                        </form>
                                    <?php } ?>
                                    <?php if ($can_delete) { ?>
                                        <form method="post" action="<?php echo site_url('admin/plan_type/delete/' . (int) $f['id']); ?>" style="display:inline"
                                              onsubmit="return confirm('Delete this feature?');"><?php echo $token; ?>
                                            <button class="btn btn-sm btn-default text-danger" type="submit" <?php echo $f['plans'] ? 'disabled title="Listed on a plan"' : 'title="Delete"'; ?>><i class="fa fa-trash"></i></button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
