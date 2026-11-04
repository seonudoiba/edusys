<?php $this->load->view('layout/course_css.php'); ?>
<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$result =   $this->customlib->getLoggedInUserData();
?>
<div class="content-wrapper">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
        <div class="subscription-info">
                <?php if ($plan_data): // Check if plan_data exists ?>
                    <div class="subscription-details">
                        <h3>Subscription Details</h3>
                        <p><strong>Created At:</strong> <?php echo $plan_data['created_at']; ?></p>
                        <?php if (isset($plan_data['remaining_days']) && $plan_data['remaining_days'] >= 0): ?>
                          <p><strong>Remaining Days:</strong> <?php echo $plan_data['remaining_days']; ?></p>
                        <?php endif; ?>
                        <p><strong>Status:</strong> <?php echo $plan_data['status']; ?></p>
                    </div>
                <?php endif; ?>
                <?php if (isset($plan_data['remaining_days']) && $plan_data['remaining_days'] < 1): ?>
                  <div class="row">
                    <div class="container spaceb50">
                      <div class="row">
                        <div class="col-md-12">
                          <?php
                          $totalPlans = count($plans);
                          $columnsPerRow = 12 / $totalPlans;
                          $columnClass = "col-md-" . (12 / $totalPlans);
                          
                          foreach ($plans as $key => $value) {
                          ?>
                            <div class="columns <?php echo $columnClass; ?>">
                              <ul class="price">
                                <li class="header">
                                  <?php echo $value['title']; ?>
                                  <div class="pricesell vh-center" style="color:white;">
                                    <?php if ($value['oprice'] != 0) { ?>
                                      <span><del>₹<?php echo $value['price']; ?></del></span>
                                    <?php } ?>
                                    ₹<?php echo $value['oprice']; ?>
                                  </div>
                                </li>
                                <?php if ($value['text']) { ?>
                                  <li class="grey"><?php echo $value['text']; ?></li>
                                <?php } ?>

                                <?php foreach ($value['list'] as $key_ => $value_) { ?>
                                  <li style="font-size: 14px;">
                                    <?php echo $value_['name']; ?>
                                  </li>
                                <?php } ?>

                                <li class="grey d-flex justify-content-center">
                                  <a href="<?php echo base_url(); ?>subscription/edit?plan_id=<?php echo $value['plan_id']; ?>&school_id=<?php echo $school_id; ?>"
                                    class="button">Buy Now</a>
                                </li>
                              </ul>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
            </div>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
    * {
        box-sizing: border-box;
    }

    .columns {
        float: left;
        padding: 8px;
    }

    .price {
        list-style-type: none;
        border: 1px solid #eee;
        margin: 0;
        padding: 0;
        -webkit-transition: 0.3s;
        transition: 0.3s;
    }

    .price:hover {
        box-shadow: 0 8px 12px 0 rgba(0, 0, 0, 0.2);
    }

    .price .header {
        background-color: #111;
        color: white;
        font-size: 25px;
    }

    .price li {
        border-bottom: 1px solid #eee;
        padding: 20px;
        text-align: center;
    }

    .price .grey {
        background-color: #eee;
        font-size: 20px;
    }

    .button {
        background-color: #04AA6D;
        border: none;
        color: white;
        padding: 10px 25px;
        text-align: center;
        text-decoration: none;
        font-size: 18px;
    }

    /* Subscription info box styles */
    .subscription-info {
        background-color: #f9f9f9;
        padding: 20px;
        margin-top: 30px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .subscription-info h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    .subscription-details p {
        font-size: 18px;
        margin: 5px 0;
    }

    .subscription-details p strong {
        color: #333;
    }

    /* Responsive layout for small screens */
    @media only screen and (max-width: 600px) {
        .columns {
            width: 100%;
        }
    }
</style>