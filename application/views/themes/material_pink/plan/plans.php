<?php 
    $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="row">
    <div class="container spaceb50">
        <div class="row">
            <div class="col-md-12">
                <?php
                $totalPlans = count($plans);
                if ($totalPlans == 0) {
                    echo "<p>No plans found.</p>";
                }else{ 
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
                                        <span><del><?= $currency_symbol ?><?php echo amountFormat($value['price']); ?></del></span>
                                    <?php } ?>
                                    <?= $currency_symbol ?><?php echo amountFormat($value['oprice']); ?>
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

                            <li class="grey">
                                <a href="<?php echo base_url(); ?>subscription/add?plan_id=<?php echo $value['plan_id']; ?>" class="button">Buy Now</a>
                            </li>
                        </ul>
                    </div>
                <?php
                }
                $columnsPerRow = 12 / $totalPlans;
                $columnClass = "col-md-" . (12 / $totalPlans);
                } ?>
            </div>
        </div>
    </div>
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
        box-shadow: 0 8px 12px 0 rgba(0, 0, 0, 0.2)
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

    @media only screen and (max-width: 600px) {
        .columns {
            width: 100%;
        }
    }
</style>