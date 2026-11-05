<?php
$e = function ($v) { return html_escape((string) $v); };
$size = function ($b) { return $b >= 1048576 ? number_format($b / 1048576, 1) . ' MB' : number_format($b / 1024, 0) . ' KB'; };
$can_add = $this->rbac->hasPrivilege('platform_settings', 'can_add');
$can_delete = $this->rbac->hasPrivilege('platform_settings', 'can_delete');
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-database"></i> Backups <small>the whole database: every school and the platform</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Backup files</h3>
                        <?php if ($can_add) { ?>
                            <div class="box-tools pull-right">
                                <form method="post" action="<?php echo site_url('admin/backups/create'); ?>" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='Backing up… keep this page open';">
                                    <?php echo $token; ?>
                                    <button class="btn btn-primary btn-sm" type="submit" <?php echo $writable ? '' : 'disabled'; ?>><i class="fa fa-plus"></i> Back up now</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead><tr><th>File</th><th>Created</th><th class="text-right">Size</th><th></th></tr></thead>
                            <tbody>
                                <?php if (!$files) { ?><tr><td colspan="4" class="text-center text-muted" style="padding:30px">No backups yet.</td></tr><?php } ?>
                                <?php foreach ($files as $f) { ?>
                                    <tr>
                                        <td><i class="fa fa-file-archive-o text-muted"></i> <?php echo $e($f['name']); ?></td>
                                        <td class="text-muted"><?php echo $e(date('d M Y H:i', $f['time'])); ?></td>
                                        <td class="text-right"><?php echo $e($size($f['size'])); ?></td>
                                        <td class="text-right" style="white-space:nowrap">
                                            <a class="btn btn-default btn-xs" href="<?php echo site_url('admin/backups/download/' . rawurlencode($f['name'])); ?>"><i class="fa fa-download"></i> Download</a>
                                            <?php if ($can_delete) { ?>
                                                <form method="post" action="<?php echo site_url('admin/backups/delete'); ?>" style="display:inline" onsubmit="return confirm('Delete <?php echo $e($f['name']); ?>?');">
                                                    <?php echo $token; ?><input type="hidden" name="name" value="<?php echo $e($f['name']); ?>">
                                                    <button class="btn btn-link btn-xs text-danger" type="submit">delete</button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Where</h3></div>
                    <div class="box-body">
                        <p><code style="word-break:break-all"><?php echo $e($dir); ?></code>
                            <?php if (!$writable) { ?><br><span class="text-danger">Not writable by the web server.</span><?php } ?></p>
                        <?php if (!$custom_dir) { ?>
                            <p class="text-muted" style="font-size:12px">Default folder inside the application (never served). Set <code>EDUSYS_BACKUP_DIR</code> to keep backups elsewhere, ideally on another disk.</p>
                        <?php } ?>
                        <p class="text-muted" style="font-size:12px">A backup on the same server is lost with the server: download copies, or schedule
                            <code>mysqldump</code> off-site (DEPLOYMENT.md, backups). Large databases are better backed up with <code>mysqldump</code> from cron than from this page.</p>
                    </div>
                </div>
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Restoring</h3></div>
                    <div class="box-body" style="font-size:12px">
                        <p>Restoring replaces <b>every</b> school's data, so it is done on the server, not from this page:</p>
                        <ol style="padding-left:18px">
                            <li>Put the site in maintenance (stop the web server or show a maintenance page).</li>
                            <li>Restore into the database:<br>
                                <code style="word-break:break-all">gunzip -c FILE.sql.gz | mysql -h <?php echo $e($db_host); ?> -u USER -p <?php echo $e($database); ?></code></li>
                            <li>Run <code>php index.php migrate</code> if the backup is from an older version.</li>
                        </ol>
                        <p>To restore a single school, use its export (Schools &gt; a school &gt; Data) instead.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
