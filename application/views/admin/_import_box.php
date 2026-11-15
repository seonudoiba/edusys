<?php
/**
 * Import box under an "Add ..." form: sample Excel download, file picker, Import button.
 * $import = array('title' => ..., 'action' => url, 'sample' => url, 'help' => html, 'id' => dom id)
 */
?>
<div class="box box-primary" id="<?php echo html_escape($import['id']); ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo html_escape($import['title']); ?></h3>
        <div class="box-tools pull-right">
            <a class="btn btn-primary btn-xs" href="<?php echo $import['sample']; ?>"><i class="fa fa-download"></i> <?php echo $this->lang->line('download_sample_import_file'); ?></a>
        </div>
    </div>
    <form action="<?php echo $import['action']; ?>" method="post" enctype="multipart/form-data" data-autoload="off">
        <?php echo $this->customlib->getCSRF(); ?>
        <div class="box-body">
            <p class="text-muted" style="margin-bottom:8px"><?php echo $import['help']; ?></p>
            <input type="file" name="file" accept=".xlsx,.csv" class="form-control" required>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-info pull-right"><i class="fa fa-upload"></i> Import</button>
        </div>
    </form>
</div>
