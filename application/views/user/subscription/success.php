<?php $this->load->view('layout/course_css.php'); ?>
<div class="content-wrapper">
  <section class="content">
    <div class="row">
    <div class="col-md-12">
    <div class="payment-success-container">
        <div class="container">
            <div class="success-icon">
                <h1>Payment Successful</h1>
                <p>Your payment has been processed successfully. Thank you for your purchase!</p>
                <a href="/user/plan/planlist" class="btn">Back to Homepage</a>
            </div>
        </div>
    </div>
    </div>
    </div>
  </section>
<style>
    .payment-success-container {
        justify-content: center;
        align-items: center;
    }

    .success-icon {
        font-size: 50px;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    h1 {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }

    p {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
    }

    .btn {
        display: inline-block;
        padding: 12px 30px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #45a049;
    }
</style>
<script>
    setTimeout(function () {
        window.location.href = "/user/plan/planlist";
    }, 5000);
</script>