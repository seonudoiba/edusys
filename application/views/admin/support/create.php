<?php $e = function ($v) { return html_escape((string) $v); }; ?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-life-ring"></i> New support request</h1></section>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <form method="post" action="<?php echo site_url('admin/support/create'); ?>">
                        <?php echo $token; ?>
                        <div class="box-body">
                            <?php if ($errors) { ?><div class="alert alert-danger"><?php echo implode('<br>', array_map($e, $errors)); ?></div><?php } ?>
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control" maxlength="255" required value="<?php echo $e($this->input->post('subject')); ?>">
                            </div>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label>Category</label>
                                    <select name="category" class="form-control">
                                        <?php foreach (Support_tickets::CATEGORIES as $k => $l) { ?>
                                            <option value="<?php echo $k; ?>" <?php echo $this->input->post('category') === $k ? 'selected' : ''; ?>><?php echo $e($l); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label>Priority</label>
                                    <select name="priority" class="form-control">
                                        <?php foreach (Support_tickets::PRIORITIES as $k => $l) { ?>
                                            <option value="<?php echo $k; ?>" <?php echo ($this->input->post('priority') ?: 'normal') === $k ? 'selected' : ''; ?>><?php echo $e($l); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" class="form-control" rows="8" maxlength="20000" required placeholder="What happened, on which page, and what you expected"><?php echo $e($this->input->post('message')); ?></textarea>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a class="btn btn-default" href="<?php echo site_url('admin/support'); ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-paper-plane"></i> Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
