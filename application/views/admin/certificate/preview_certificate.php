<?php
$cert = $certificate;
$has_custom_bg = !empty($cert->background_image);
$school_data = isset($sch_setting) ? $sch_setting : (isset($this->sch_setting_detail) ? $this->sch_setting_detail : null);
$student = isset($sample_student) ? $sample_student : get_certificate_sample_student();

$parsed_body = parse_certificate_tags($cert->certificate_text, $student, $school_data);
$parsed_left_hdr   = parse_certificate_tags($cert->left_header, $student, $school_data);
$parsed_center_hdr = parse_certificate_tags($cert->center_header, $student, $school_data);
$parsed_right_hdr  = parse_certificate_tags($cert->right_header, $student, $school_data);
$parsed_left_ftr   = parse_certificate_tags($cert->left_footer, $student, $school_data);
$parsed_center_ftr = parse_certificate_tags($cert->center_footer, $student, $school_data);
$parsed_right_ftr  = parse_certificate_tags($cert->right_footer, $student, $school_data);

$student_photo_url = $this->media_storage->getImageURL('uploads/student_images/no_image.png');
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Playfair+Display:ital,wght@0,500;0,700;1,400&family=Montserrat:wght@400;500;600;700&display=swap">

<style type="text/css">
    .cert-preview-wrapper {
        width: 100%;
        max-width: 960px;
        margin: 0 auto;
        aspect-ratio: 297 / 210;
        position: relative;
        overflow: hidden;
        border-radius: 4px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        background: #fff;
        font-family: 'Montserrat', Arial, sans-serif;
    }

    /* Custom Background Overlay */
    .cert-prev-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: fill;
        z-index: 1;
    }

    .cert-prev-overlay {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
    }

    .cert-prev-photo {
        position: absolute;
        right: 4%;
        z-index: 3;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        border-radius: 3px;
        overflow: hidden;
        background: #fff;
    }
    .cert-prev-photo img {
        width: 80px;
        height: 95px;
        object-fit: cover;
        display: block;
    }

    .cert-prev-header {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 600;
        color: #1a1a1a;
    }
    .cert-prev-header .hdr-left { text-align: left; flex: 1; }
    .cert-prev-header .hdr-center { text-align: center; flex: 2; font-family: 'Cinzel', serif; font-size: 16px; }
    .cert-prev-header .hdr-right { text-align: right; flex: 1; }

    .cert-prev-body {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        font-size: 14px;
        line-height: 24px;
        color: #222;
        font-family: 'Playfair Display', Georgia, serif;
    }

    .cert-prev-footer {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 12px;
        color: #222;
    }
    .cert-prev-footer .ftr-left { text-align: left; flex: 1; }
    .cert-prev-footer .ftr-center { text-align: center; flex: 1; }
    .cert-prev-footer .ftr-right { text-align: right; flex: 1; }

    /* Built-in Ornate Frame Mode */
    .cert-prev-ornate {
        width: 100%;
        height: 100%;
        padding: 4%;
        background: radial-gradient(circle at center, #ffffff 60%, #faf6ee 100%);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cert-prev-border {
        border: 3px double #b38728;
        outline: 1.5px solid #1a2a44;
        outline-offset: -5px;
        width: 100%;
        height: 100%;
        padding: 4% 5% 3%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .prev-corner {
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #b38728;
        border-style: solid;
    }
    .prev-corner-tl { top: 0px; left: 0px; border-width: 3px 0 0 3px; }
    .prev-corner-tr { top: 0px; right: 0px; border-width: 3px 3px 0 0; }
    .prev-corner-bl { bottom: 0px; left: 0px; border-width: 0 0 3px 3px; }
    .prev-corner-br { bottom: 0px; right: 0px; border-width: 0 3px 3px 0; }

    .cert-prev-school-name {
        font-family: 'Cinzel', Georgia, serif;
        font-size: 20px;
        font-weight: 800;
        color: #1a2a44;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        text-align: center;
    }
    .cert-prev-school-sub {
        font-size: 11px;
        color: #555;
        text-align: center;
        letter-spacing: 0.5px;
    }
    .cert-prev-meta {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 600;
        color: #444;
        margin-top: 4px;
        border-bottom: 1px solid rgba(179, 135, 40, 0.3);
        padding-bottom: 4px;
    }

    .cert-prev-title {
        font-family: 'Cinzel', Georgia, serif;
        font-size: 20px;
        font-weight: 700;
        color: #b38728;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-align: center;
        margin: 6px 0;
    }

    .cert-prev-body-ornate {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 14px;
        line-height: 24px;
        color: #222;
        text-align: center;
        max-width: 88%;
        margin: auto;
    }

    .cert-prev-sigs {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding-top: 8px;
    }
    .cert-prev-sig {
        text-align: center;
        min-width: 120px;
    }
    .prev-sig-line {
        border-top: 1px solid #1a2a44;
        margin-bottom: 4px;
    }
    .prev-sig-title {
        font-size: 11px;
        font-weight: 700;
        color: #1a2a44;
        text-transform: uppercase;
    }
</style>

<div class="cert-preview-wrapper">
    <?php if ($has_custom_bg) { ?>
        <img class="cert-prev-bg-img" src="<?php echo $this->media_storage->getImageURL('uploads/certificate/' . $cert->background_image); ?>" alt="Certificate Background">

        <div class="cert-prev-overlay">
            <?php if ($cert->enable_student_image == 1) { ?>
                <div class="cert-prev-photo" style="top: <?php echo !empty($cert->enable_image_height) ? (int)($cert->enable_image_height * 0.75) : 80; ?>px;">
                    <img src="<?php echo $student_photo_url; ?>" alt="Student Photo">
                </div>
            <?php } ?>

            <?php
            // Scale px values proportionally to preview container
            $c_width = !empty($cert->content_width) ? (int)$cert->content_width : 820;
            $h_top   = !empty($cert->header_height) ? (int)($cert->header_height * 0.75) : 120;
            $b_top   = !empty($cert->content_height) ? (int)($cert->content_height * 0.75) : 220;
            $f_top   = !empty($cert->footer_height) ? (int)($cert->footer_height * 0.75) : 380;
            ?>

            <div class="cert-prev-header" style="width: 85%; top: <?php echo $h_top; ?>px;">
                <div class="hdr-left"><?php echo $parsed_left_hdr; ?></div>
                <div class="hdr-center"><?php echo $parsed_center_hdr; ?></div>
                <div class="hdr-right"><?php echo $parsed_right_hdr; ?></div>
            </div>

            <div class="cert-prev-body" style="width: 85%; top: <?php echo $b_top; ?>px;">
                <?php echo $parsed_body; ?>
            </div>

            <div class="cert-prev-footer" style="width: 85%; top: <?php echo $f_top; ?>px;">
                <div class="ftr-left"><?php echo $parsed_left_ftr; ?></div>
                <div class="ftr-center"><?php echo $parsed_center_ftr; ?></div>
                <div class="ftr-right"><?php echo $parsed_right_ftr; ?></div>
            </div>
        </div>

    <?php } else { ?>
        <!-- Built-in Ornate Frame Preview -->
        <div class="cert-prev-ornate">
            <div class="cert-prev-border">
                <div class="prev-corner prev-corner-tl"></div>
                <div class="prev-corner prev-corner-tr"></div>
                <div class="prev-corner prev-corner-bl"></div>
                <div class="prev-corner prev-corner-br"></div>

                <div>
                    <div class="cert-prev-school-name">
                        <?php echo !empty($school_data->name) ? html_escape($school_data->name) : 'ACADEMY OF EXCELLENCE'; ?>
                    </div>
                    <?php if (!empty($school_data->address)) { ?>
                        <div class="cert-prev-school-sub"><?php echo html_escape($school_data->address); ?></div>
                    <?php } ?>

                    <div class="cert-prev-meta">
                        <div><?php echo !empty($parsed_left_hdr) ? $parsed_left_hdr : 'Ref No: ' . html_escape($student->admission_no); ?></div>
                        <div><?php echo !empty($parsed_center_hdr) ? $parsed_center_hdr : ''; ?></div>
                        <div><?php echo !empty($parsed_right_hdr) ? $parsed_right_hdr : 'Date: ' . date('d-m-Y'); ?></div>
                    </div>
                </div>

                <div>
                    <div class="cert-prev-title">
                        <?php echo !empty($cert->certificate_name) ? html_escape($cert->certificate_name) : 'CERTIFICATE OF RECOGNITION'; ?>
                    </div>

                    <div class="cert-prev-body-ornate">
                        <?php echo $parsed_body; ?>
                    </div>
                </div>

                <div class="cert-prev-sigs">
                    <div class="cert-prev-sig">
                        <div class="prev-sig-line"></div>
                        <div class="prev-sig-title"><?php echo !empty($parsed_left_ftr) ? $parsed_left_ftr : 'Class Teacher'; ?></div>
                    </div>

                    <div class="cert-prev-sig">
                        <?php if (!empty($parsed_center_ftr)) { ?>
                            <div class="prev-sig-line"></div>
                            <div class="prev-sig-title"><?php echo $parsed_center_ftr; ?></div>
                        <?php } ?>
                    </div>

                    <div class="cert-prev-sig">
                        <div class="prev-sig-line"></div>
                        <div class="prev-sig-title"><?php echo !empty($parsed_right_ftr) ? $parsed_right_ftr : 'Principal'; ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
