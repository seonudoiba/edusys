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
                        <?php 
                        if ($this->session->flashdata('msg')) {
                            echo '<div class="alert alert-success">' . htmlspecialchars($this->session->flashdata('msg')) . '</div>';
                        }
                        ?>
                    </div>
                    <div class="ptt10">
                        <div class="bordertop">
                            <div class="box-header with-border">
                                <h3 class="box-title titlefix"> School List
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="download_label"><?php echo $this->lang->line('admission_enquiry_list'); ?>
                                </div>
                                <div class="mailbox-messages">
                                    <div class="table-responsive overflow-visible-lg">
                                        <table class="table table-hover table-striped table-bordered" id="plantable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>School Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Email Address</th>
                                                    <th>Website</th>
                                                    <th>Status</th>
                                                    <th>Payment Status</th>
                                                    <th>School Status</th>
                                                    <th>Expire Date</th>
                                                    <th>Created Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (empty($schools_data)) {
                                                    ?>
                                                <?php
                                                } else {
                                                    foreach ($schools_data as $key => $school) {
                                                        ?>
                                                <tr>
                                                    <td class="mailbox-name"><?php echo $school['id']; ?> </td>
                                                    <td class="mailbox-name"><?php echo $school['school_name']; ?> </td>
                                                    <td class="mailbox-name"><?php echo $school['phone_number']; ?>
                                                    </td>
                                                    <td class="mailbox-name"><?php echo $school['email_address']; ?>
                                                    </td>
                                                    <td class="mailbox-name">
                                                        <?php
                                                                if (!empty($school['subdomain'])) {
                                                                    $school_url = (is_https() ? 'https' : 'http') . '://' . $school['subdomain'] . '.' . $this->config->item('tenancy_base_domain') . '/';
                                                                    if ($school['cron_flag'] == 1) { ?>
                                                        <a href="<?php echo html_escape($school_url); ?>" target="_blank"><?php echo html_escape($school_url); ?></a>
                                                        <?php } else { echo html_escape($school_url) . ' <small>(not set up yet)</small>'; }
                                                                } else { echo '<small class="text-muted">No subdomain</small>'; }
                                                            ?>
                                                    </td>
                                                    <td class="mailbox-name">
                                                        <?php
                                                                echo ($school['status'] == 1) ? 'Enabled' : 'Disabled';
                                                                ?>
                                                    </td>
                                                    <td class="mailbox-name">
                                                        <?php

                                                                    if($school['payment_status'] == 0){
                                                                        echo 'Pending';
                                                                    }elseif($school['payment_status'] == 1){
                                                                        echo 'Paid';
                                                                    }elseif($school['payment_status'] == 2){
                                                                        echo 'Expired';
                                                                    }

                                                                ?>
                                                    </td>
                                                    <?php 
                                                                if($school['cron_flag'] == 0){
                                                                    $cron_flag = "Pending";
                                                                    $class = "orange";
                                                                } else if($school['cron_flag'] == 1){
                                                                    $cron_flag = "Completed";
                                                                    $class = "green";
                                                                } else if($school['cron_flag'] == 2){
                                                                    $cron_flag = "Processing";
                                                                    $class = "blue";
                                                                }
                                                            ?>
                                                    <td class="status-column">
                                                        <div style="color: <?php echo $class; ?> ">
                                                            <?php echo $cron_flag; ?></div>
                                                    </td>
                                                    <?php 
                                                                $given_date_obj = '';
                                                                if(!empty($school['expiry_date'])){
                                                                    $given_date_obj = new DateTime($school['expiry_date']);
                                                                }
                                                                $current_date_obj = new DateTime();
                                                            
                                                                if(empty($school['expiry_date'])){
                                                                    $expiry_date = "-";
                                                                    $class = "";
                                                                } else if($given_date_obj < $current_date_obj){
                                                                    $class = "red";
                                                                    $expiry_date = date('d-m-Y', strtotime($school['expiry_date']));
                                                                } else if($given_date_obj > $current_date_obj){
                                                                    $class = "green";
                                                                    $expiry_date = date('d-m-Y', strtotime($school['expiry_date']));
                                                                }
                                                            ?>
                                                    <td class="status-column">
                                                        <div style="color: <?php echo $class; ?> ">
                                                            <?php echo $expiry_date; ?>
                                                            <?php
                                                                    if(!empty($school['remainingDays'])){ ?>
                                                            (<?php echo $school['remainingDays']; ?> Days)
                                                            <?php
                                                                    }
                                                                    ?>
                                                        </div>
                                                    </td>
                                                    <td class="mailbox-name"><?php echo $school['created_at']; ?> </td>
                                                    <td class="mailbox-date text-right white-space-nowrap">
                                                        <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_edit')) { ?>

                                                        <a href="<?php echo site_url('admin/schools/delete/' . $school['id']); ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this school?')"><i
                                                                class="fa fa-trash"></i></a>

                                                        <?php }
                                                                ?>
                                                        <?php if ($school['payment_status'] == '1' && $school['cron_flag'] == 0) { ?>
                                                        <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_delete')) { 
                                                                        $class="change-status";
                                                                        $buttonClass = 'btn-primary btn-sm';
                                                                        $text = $this->lang->line('active');    
                                                                        $status = $school['status'] ? $school['status'] : 0;
                                                                        ?>
                                                        <a class="btn btn-primary change-status" id="active_"
                                                            data-id="<?php echo $school['id']; ?>">
                                                            <?php echo $this->lang->line('active'); ?>
                                                        </a>
                                                        <?php 
                                                                    ?>
                                                        <?php }
                                                                } else if($school['payment_status'] == '1' && $school['cron_flag'] == 1 && $school['status'] == 1){ ?>
                                                        <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_delete')) { 
                                                                        $class="enabled_status";
                                                                        $buttonClass = 'btn-danger btn-sm';
                                                                        $text = $this->lang->line('disabled_status');
                                                                        $status = $school['status'];
                                                                        ?>
                                                        <a class="btn <?php echo $buttonClass; ?> <?php echo $class; ?>"
                                                            id="<?php echo $school['id']; ?>"
                                                            data-status="<?php echo $status; ?>">
                                                            <?php echo $text; ?>
                                                        </a>
                                                        <?php
                                                                     }
                                                                } else if($school['payment_status'] == '1' && $school['cron_flag'] == 1 && $school['status'] == 0){ ?>
                                                        <?php if ($this->rbac->hasPrivilege('admission_enquiry', 'can_delete')) { 
                                                                        $class="enabled_status";
                                                                        $buttonClass = 'btn-success btn-sm';
                                                                        $text = $this->lang->line('enabled_status');
                                                                        $status = $school['status'];
                                                                    ?>
                                                        <a class="btn <?php echo $buttonClass; ?> <?php echo $class; ?>"
                                                            id="<?php echo $school['id']; ?>"
                                                            data-status="<?php echo $status; ?>">
                                                            <?php echo $text; ?>
                                                        </a>
                                                        <?php }
                                                                }
                                                                ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#plantable").DataTable({
        searching: true,
        paging: true,
        bSort: false,
        info: false,
        dom: "Bfrtip",
        buttons: [{
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
        aoColumnDefs: [{
            bSortable: true,
            aTargets: ['noExport1']
        }]
    });
});
</script>
<script type="text/javascript">
$(document).ready(function() {
    $("#btnreset").click(function() {
        $("#form1")[0].reset();
    });
});

// Approve a school application.
$(document).on('click', '.change-status', function() {
    var schoolId = $(this).data('id');

    $.ajax({
        url: '<?php echo base_url(); ?>admin/schools/update_status/',
        type: 'POST',
        data: {
            id: schoolId
        },
        success: function(response) {
            // Approve sets the school up (or explains why it can't, e.g. no subdomain yet).
            alert(response.message || (response.status === 'success' ? 'Done.' : 'Something went wrong.'));
            if (response.status === 'success') {
                location.reload();
            }
        },
        error: function() {
            alert('Setting up the school failed. Please try again.');
        }
    });
});
</script>
<script type="text/javascript">
//on button click
$(document).on('click', '.enabled_status', function() {
    var schoolId = $(this).attr('id');
    var status = $(this).data('status');
    $.ajax({
        url: '<?php echo base_url(); ?>admin/schools/status_update/' + schoolId + '/' + status,
        type: 'GET',
        success: function(response) {
            var data = JSON.parse(response);

            if (data.status == 'success') {

                location.reload();
            }
        },
    });
});
</script>