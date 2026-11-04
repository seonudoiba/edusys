<?php 
    $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
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

        .select2-selection__choice {
            background-color: #000000 !important;
        }
    </style>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="col-md-12">
                        <?php //echo $this->session->flashdata('msg');
                        //$this->session->unset_userdata('msg'); ?>
                    </div>
                    <div class="ptt10">
                        <div class="bordertop">
                            <div class="box-header with-border">
                                <h3 class="box-title titlefix"> <?php echo $this->lang->line('subscription'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="download_label"><?php echo $this->lang->line('admission_enquiry_list'); ?>
                                </div>
                                <div class="mailbox-messages">
                                    <div class="table-responsive overflow-visible-lg">
                                        <table class="table table-hover table-striped table-bordered" id="enquirytable">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('plan'); ?></th>
                                                    <th><?php echo $this->lang->line('school_name'); ?></th>
                                                    <th><?php echo $this->lang->line('owner_name'); ?></th>
                                                    <th><?php echo $this->lang->line('payment_method'); ?></th>
                                                    <th><?php echo $this->lang->line('payment_status'); ?></th>
                                                    <th><?php echo $this->lang->line('total'); ?></th>
                                                    <th><?php echo $this->lang->line('created_at'); ?></th>
                                                    <th class="text-right noExport1">
                                                        <?php echo $this->lang->line('action'); ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (empty($subscription_list)) {
                                                    ?>
                                                    <?php
                                                } else {
                                                    foreach ($subscription_list as $key => $value) {
                                                        ?>
                                                        <tr>
                                                            <td class="mailbox-name"><?php echo $value['plan_name']; ?> </td>
                                                            <td class="mailbox-name"><?php echo $value['school_name']; ?> </td>
                                                            <td class="mailbox-name"><?php echo $value['owner_name'] ? $value['owner_name'] : '-'; ?> </td>
                                                            <td class="mailbox-name"><?php echo $value['payment_method']; ?>
                                                            </td>
                                                            <td class="mailbox-name"><?php echo $value['payment_status']; ?>
                                                            </td>
                                                            <td class="mailbox-name"><?= $currency_symbol ?><?php echo amountFormat($value['total']); ?></td>                                                            
                                                            <td class="mailbox-name"><?php echo $value['created_at']; ?></td>
                                                            <td class="mailbox-date text-right white-space-nowrap">
                                                                <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_delete')) { ?>
                                                                    <a href="#" class="btn btn-default btn-xs delete-button"
                                                                        data-toggle="tooltip" title=""
                                                                        data-sub-id="<?php echo $value['id']; ?>"
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
                    <h4 class="box-title"> <?php echo $this->lang->line('plan'); ?></h4>
                </div>
                <div class="modal-body pt0 pb0">
                    <form id="formadd" method="post" class="ptt10">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('title'); ?></label><small
                                                class="req"> *</small>
                                            <input type="text" id="title" autocomplete="off" class="form-control"
                                                value="<?php echo set_value('title'); ?>" name="title">
                                            <span id="name_add_error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('text'); ?></label><small
                                                class="req"> *</small> <small class="req"><span
                                                    id="phone_error_message"></span></small>
                                            <input id="number" autocomplete="off" name="text" placeholder="" type="text"
                                                class="form-control" value="<?php echo set_value('text'); ?>" />
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
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label
                                                    for="plan_type_list"><?php echo $this->lang->line('plan_type_list'); ?></label><small
                                                    class="req"> *</small>
                                                <select id="plan_type_list" name="plan_type_list[]" class="form-control"
                                                    multiple>
                                                    <?php
                                                    foreach ($plan_type_list as $value => $label): ?>
                                                        <option value="<?php echo $label['id']; ?>" <?php echo (in_array($value, set_value('multi_select', []))) ? 'selected' : ''; ?>>
                                                            <?php echo $label['name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span id="plan_type_list_error" class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div><!--./row-->
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        function delete_enquiry(subscription_id) {
            if (confirm("Are you sure you want to delete this plan?")) {
                $.ajax({
                    url: "<?php echo site_url("admin/Subscription_list/delete/") ?>" + subscription_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        console.log('response', response);
                        console.log('response.status', response.status);
                        if (response.status == 'success') {
                            successMsg(response.message);
                            location.reload();
                        } else {
                            errorMsg(response.message);
                        }
                    },
                    error: function () {
                        alert('There was an error with the request.');
                    }
                });
            }
        }

        $(".delete-button").on('click', function (e) {
            console.log('delete-button');
            e.preventDefault();
            var subscription_id = $(this).data('sub-id');
            delete_enquiry(subscription_id);
        });

    });
</script>

<script>
    $(document).ready(function () {
        $('#plan_type_list').select2();

        // Initialize modals
        $('#myModal,#follow_up,#myModaledit').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

        // Open modal when add button is clicked
        $(".openmodal").click(function () {
            $('#formadd').trigger("reset");
            $("#myModal").modal();
        });

        // Re-initialize Select2 when modal is shown
        $("#myModal").on("shown.bs.modal", function () {
            $('#plan_type_list').select2();
        });

    });

    $("#formadd").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
        $.ajax({
            url: "<?php echo site_url("admin/plan/add/") ?>",
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

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#enquirytable").DataTable({
            searching: true,
            paging: true,
            bSort: false,
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
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: ['noExport1']
                }
            ]
        });
    });
</script>