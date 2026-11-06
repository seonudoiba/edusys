<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap"><h1><?php echo html_escape($page['title']); ?></h1></div>
</section>
<div class="wrap">
    <div class="page-body prose"><?php echo $page['description']; ?></div>
</div>
