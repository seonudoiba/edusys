<?php $this->load->view('layout/course_css.php'); ?>
<div class="content-wrapper">
  <section class="content">
    <div class="row">
    <div class="col-md-12">
    <div class="payment-failure-container">
        <div class="container">
            <div class="success-icon">
            <h1>Payment Failed</h1>
            <p>Oops! Something went wrong with your payment. Please try again later or contact support.</p>
            <a href="/user/plan/planlist" class="btn">Back to Homepage</a>
            </div>
        </div>
    </div>
    </div>
    </div>
  </section>
<style>
    .payment-failure-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .failure-icon {
        font-size: 50px;
        color: #dc3545;
        margin-bottom: 20px;
    }

    h1 {
        font-size: 28px;
        color: #dc3545;
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
        background-color: #dc3545;
        color: white;
        font-size: 16px;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #c82333;
    }

    .retry {
        background-color: #ffc107;
        margin-left: 10px;
    }

    .retry:hover {
        background-color: #e0a800;
    }
</style>
<script>
    setTimeout(function () {
        window.location.href = "/user/plan/planlist";
    }, 5000);
</script>