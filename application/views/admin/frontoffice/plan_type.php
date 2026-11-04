<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?>
        </h1>
    </section>
    <style>
        .ml-20 {
            margin-left: 5rem;
        }
    </style>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="col-md-12">
                        <?php echo $this->session->flashdata('msg');
                        $this->session->unset_userdata('msg'); ?>
                    </div>
                    <div class="ptt10">
                        <div class="bordertop">
                            <div class="box-header with-border">
                                <h3 class="box-title titlefix"> <?php echo $this->lang->line('plan_type'); ?>
                                </h3>
                                <div class="box-tools pull-right">
                                    <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_add')) { ?>
                                        <button type="button" class="btn btn-sm btn-primary openmodal"><i
                                                class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="download_label"><?php echo $this->lang->line('admission_enquiry_list'); ?>
                                </div>
                                <div class="mailbox-messages">
                                    <div class="table-responsive overflow-visible-lg">
                                        <table class="table table-hover table-striped table-bordered" id="enquirytable">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('name'); ?></th>
                                                    <th><?php echo $this->lang->line('sort_order'); ?></th>
                                                    <th><?php echo $this->lang->line('status'); ?></th>
                                                    <th><?php echo $this->lang->line('created_at'); ?></th>
                                                    <th class="text-right noExport1">
                                                        <?php echo $this->lang->line('action'); ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                if (empty($plan_list)) {
                                                    ?>
                                                    <?php
                                                } else {
                                                    foreach ($plan_list as $key => $value) {
                                                        ?>
                                                        <tr>
                                                            <td class="mailbox-name"><?php echo $value['name']; ?> </td>
                                                            <td class="mailbox-name"><?php echo $value['sort_order']; ?> </td>
                                                            <td class="mailbox-name">
                                                                <?php
                                                                echo ($value['status'] == 1) ? 'Enabled' : 'Disabled';
                                                                ?>
                                                            </td>
                                                            <td class="mailbox-name">
                                                                <?php echo $value['created_at']; ?>
                                                            </td>
                                                            <td class="mailbox-date text-right white-space-nowrap">
                                                                <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_edit')) { ?>
                                                                    <a onclick="getRecord('<?php echo $value['id']; ?>')"
                                                                        class="btn btn-default btn-xs" data-target="#myModaledit"
                                                                        data-toggle="modal"
                                                                        title="<?php echo $this->lang->line('edit'); ?>"><i
                                                                            class="fa fa-pencil"></i>
                                                                    </a>
                                                                <?php }
                                                                ?>
                                                                <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_delete')) { ?>
                                                                    <a href="#" class="btn btn-default btn-xs" data-toggle="tooltip"
                                                                        title=""
                                                                        onclick="delete_enquiry('<?php echo $value["id"] ?>')"
                                                                        data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                                        <i class="fa fa-remove"></i>
                                                                    </a>
                                                                <?php }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table><!-- /.table -->
                                    </div>
                                </div><!-- /.mail-box-messages -->
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="box-title"> <?php echo $this->lang->line('plan_type'); ?></h4>
                </div>
                <div class="modal-body pt0 pb0">
                    <form id="formadd" method="post" class="ptt10">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label
                                                for="pwd"><?php echo $this->lang->line('plan_type_name'); ?></label><small
                                                class="req"> *</small>
                                            <input type="text" id="name_add" autocomplete="off" class="form-control"
                                                value="<?php echo set_value('name'); ?>" name="name">
                                            <span id="name_add_error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">
                                                <?php echo $this->lang->line('status'); ?>
                                            </label><small class="req"> *</small>
                                            <select id="status" name="status" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <option value="1" <?php echo set_value('status', (isset($school['status']) && $school['status'] == '1')) ? 'selected' : ''; ?>>
                                                    <?php echo $this->lang->line('enable'); ?>
                                                </option>
                                                <option value="2" <?php echo set_value('status', (isset($school['status']) && $school['status'] == '2')) ? 'selected' : ''; ?>>
                                                    <?php echo $this->lang->line('disable'); ?>
                                                </option>
                                            </select>
                                            <span class="text-danger">
                                                <?php echo form_error('status'); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label
                                                for="pwd"><?php echo $this->lang->line('sort_order'); ?></label><small
                                                class="req"> *</small> <small class="req"><span
                                                    id="phone_error_message"></span></small>
                                            <input id="sort_order" autocomplete="off" name="sort_order" placeholder=""
                                                type="text" class="form-control"
                                                value="<?php echo set_value('sort_order'); ?>" />
                                        </div>
                                    </div>
                                </div><!--./row-->
                            </div><!--./col-md-12-->
                        </div><!--./row-->
                        <div class="row">
                            <div class="box-footer col-md-12">
                                <button type="submit" class="btn btn-info pull-right" id="submit"
                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="box-title"><?php echo $this->lang->line('edit_plan_list'); ?></h4>
                </div>
                <div class="modal-body pt0 pb0" id="getdetails">
                    <div id="alert_message">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="follow_up" tabindex="-1" role="dialog" aria-labelledby="follow_up">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" onclick="update()" data-dismiss="modal">&times;</button>
                    <h4 class="box-title"><?php echo $this->lang->line('follow_up_admission_enquiry'); ?></h4>
                </div>
                <div class="modal-body pt0 pb0 pr-xl-1" id="getdetails_follow_up">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('#myModal,#follow_up,#myModaledit').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });
    $(".openmodal").click(function () {
        $('#formadd').trigger("reset");
        $("#myModal").modal();
    });
</script>
<script>
    $(document).ready(function () {
        moment.lang('en', {
            week: { dow: start_week }
        });
        $('#enquiry_date').daterangepicker(
            {
                locale: {
                    format: calendar_date_time_format
                }
            });
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/plan_type/details/' + id,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }

    function postRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/plan_type/editpost/' + id,
            type: 'POST',
            data: $("#myForm1").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    $("#formadd").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
        $.ajax({
            url: "<?php echo site_url("admin/plan_type/add/") ?>",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $this.button('loading');

            },
            success: function (res) {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(res.message);
                    window.location.reload(true);
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    }));

    function delete_enquiry(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/plan_type/delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                }
            })
        }
    }

    function follow_up(id, status, created_by) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/follow_up/' + id + '/' + status + '/' + created_by,
            success: function (data) {
                $('#getdetails_follow_up').html(data);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/follow_up_list/' + id,
                    success: function (data) {
                        $('#timeline').html(data);
                    },
                    error: function () {
                        alert("<?php echo $this->lang->line('fail'); ?>");
                    }
                });
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    function update() {
        window.location.reload(true);
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#enquirytable").DataTable({
            searching: true,
            paging: true,
            bSort: true,
            info: false,
            dom: "Bfrtip",
            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'

                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                },

                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
    });

    $('#number').blur(function () {
        $('#phone_error_message').html('');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/check_number',
            type: 'POST',
            data: { phone_number: $('#number').val() },
            dataType: 'json',
            success: function (data) {
                if (data.status == "success") {
                    $('#phone_error_message').html('(' + data.message + ')');
                }
            }
        })
    })
</script>