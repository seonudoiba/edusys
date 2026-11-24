<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo !empty($certificate[0]->certificate_name) ? html_escape($certificate[0]->certificate_name) : 'Student Certificate'; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Playfair+Display:ital,wght@0,500;0,700;1,400&family=Montserrat:wght@400;500;600;700&display=swap">
    <style type="text/css">
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background-color: #525659;
            color: #1a1a1a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0;
            padding: 0;
        }

        /* Top Action Bar for screen viewing */
        .print-action-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 52px;
            background: #1e293b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 99999;
        }

        .print-action-bar h2 {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.5px;
            color: #f1f5f9;
        }

        .print-action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-print-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-print-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-print-primary:hover {
            background: #1d4ed8;
        }

        .btn-print-secondary {
            background: #475569;
            color: #f8fafc;
        }
        .btn-print-secondary:hover {
            background: #334155;
        }

        .certificate-wrapper {
            padding: 70px 0 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        /* Physical A4 Landscape: 297mm x 210mm */
        .certificate-page {
            width: 297mm;
            height: 210mm;
            max-width: 297mm;
            max-height: 210mm;
            background: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            margin: 0 auto;
            page-break-after: always;
            break-after: page;
        }

        /* Custom Background Mode */
        .cert-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: fill;
            z-index: 1;
        }

        .cert-custom-overlay {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
        }

        .cert-custom-photo {
            position: absolute;
            right: 40px;
            z-index: 3;
            border: 3px solid #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }

        .cert-custom-photo img {
            width: 100px;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .cert-custom-header {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .cert-custom-header .hdr-left { text-align: left; flex: 1; }
        .cert-custom-header .hdr-center { text-align: center; flex: 2; font-family: 'Cinzel', serif; font-size: 20px; letter-spacing: 1px; }
        .cert-custom-header .hdr-right { text-align: right; flex: 1; }

        .cert-custom-body {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-size: 16px;
            line-height: 30px;
            color: #222;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .cert-custom-footer {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 14px;
            color: #222;
        }

        .cert-custom-footer .ftr-left { text-align: left; flex: 1; }
        .cert-custom-footer .ftr-center { text-align: center; flex: 1; }
        .cert-custom-footer .ftr-right { text-align: right; flex: 1; }

        /* Built-in Ornate Professional Certificate Mode (No Background Uploaded) */
        .cert-ornate-container {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            padding: 12mm;
            background: radial-gradient(circle at center, #ffffff 60%, #faf6ee 100%);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cert-outer-border {
            border: 4px double #b38728;
            outline: 2px solid #1a2a44;
            outline-offset: -7px;
            width: 100%;
            height: 100%;
            padding: 10mm 14mm 8mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Ornate Corner Accents */
        .corner-accent {
            position: absolute;
            width: 32px;
            height: 32px;
            border-color: #b38728;
            border-style: solid;
        }
        .corner-tl { top: 0px; left: 0px; border-width: 4px 0 0 4px; }
        .corner-tr { top: 0px; right: 0px; border-width: 4px 4px 0 0; }
        .corner-bl { bottom: 0px; left: 0px; border-width: 0 0 4px 4px; }
        .corner-br { bottom: 0px; right: 0px; border-width: 0 4px 4px 0; }

        .cert-school-header {
            text-align: center;
            position: relative;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(179, 135, 40, 0.4);
        }

        .cert-school-name {
            font-family: 'Cinzel', 'Georgia', serif;
            font-size: 26px;
            font-weight: 800;
            color: #1a2a44;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .cert-school-sub {
            font-size: 12px;
            color: #555;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .cert-header-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #444;
        }

        .cert-title-section {
            text-align: center;
            margin: 10px 0 5px;
        }

        .cert-main-title {
            font-family: 'Cinzel', 'Georgia', serif;
            font-size: 28px;
            font-weight: 700;
            color: #b38728;
            letter-spacing: 3px;
            text-transform: uppercase;
            display: inline-block;
            position: relative;
            padding: 0 20px;
        }

        .cert-main-title::before,
        .cert-main-title::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40px;
            height: 2px;
            background: #b38728;
        }
        .cert-main-title::before { left: -45px; }
        .cert-main-title::after { right: -45px; }

        .cert-content-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 10px 20px;
        }

        .cert-student-photo-frame {
            position: absolute;
            top: 0px;
            right: 15px;
            border: 3px solid #b38728;
            padding: 2px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .cert-student-photo-frame img {
            width: 95px;
            height: 115px;
            object-fit: cover;
            display: block;
        }

        .cert-body-paragraph {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 17px;
            line-height: 32px;
            color: #222;
            text-align: center;
            max-width: 86%;
            margin: 0 auto;
        }

        .cert-signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 10px 20px 0;
            margin-top: 5px;
        }

        .cert-sig-block {
            text-align: center;
            min-width: 170px;
        }

        .sig-line {
            width: 100%;
            border-top: 1.5px solid #1a2a44;
            margin-bottom: 6px;
        }

        .sig-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a2a44;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sig-subtitle {
            font-size: 11px;
            color: #666;
        }

        /* Print Specific Rules */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            .no-print, .print-action-bar {
                display: none !important;
            }

            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .certificate-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                gap: 0 !important;
                display: block !important;
            }

            .certificate-page {
                box-shadow: none !important;
                margin: 0 !important;
                width: 297mm !important;
                height: 210mm !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .certificate-page:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-action-bar no-print">
        <h2>
            <i class="fa fa-certificate"></i>
            <?php echo !empty($certificate[0]->certificate_name) ? html_escape($certificate[0]->certificate_name) : 'Certificate'; ?>
            (<?php echo count($students); ?> <?php echo count($students) === 1 ? 'Record' : 'Records'; ?>)
        </h2>
        <div class="print-action-buttons">
            <button type="button" class="btn-print-action btn-print-primary" onclick="triggerPrint()">
                &#128438; Print Certificates
            </button>
            <button type="button" class="btn-print-action btn-print-secondary" onclick="window.close()">
                &#10006; Close
            </button>
        </div>
    </div>

    <div class="certificate-wrapper">
        <?php
        $cert = $certificate[0];
        $has_custom_bg = !empty($cert->background_image);
        $school_data = isset($sch_setting) ? $sch_setting : (isset($this->sch_setting_detail) ? $this->sch_setting_detail : null);
        $custom_fields_list = isset($custom_fields) ? $custom_fields : array();

        foreach ($students as $student) {
            $parsed_body = parse_certificate_tags($cert->certificate_text, $student, $school_data, null, $custom_fields_list);
            $parsed_left_hdr   = parse_certificate_tags($cert->left_header, $student, $school_data, null, $custom_fields_list);
            $parsed_center_hdr = parse_certificate_tags($cert->center_header, $student, $school_data, null, $custom_fields_list);
            $parsed_right_hdr  = parse_certificate_tags($cert->right_header, $student, $school_data, null, $custom_fields_list);
            $parsed_left_ftr   = parse_certificate_tags($cert->left_footer, $student, $school_data, null, $custom_fields_list);
            $parsed_center_ftr = parse_certificate_tags($cert->center_footer, $student, $school_data, null, $custom_fields_list);
            $parsed_right_ftr  = parse_certificate_tags($cert->right_footer, $student, $school_data, null, $custom_fields_list);

            $student_photo_url = '';
            if ($cert->enable_student_image == 1) {
                if (!empty($student->image)) {
                    $student_photo_url = $this->media_storage->getImageURL($student->image);
                } else {
                    $student_photo_url = $this->media_storage->getImageURL('uploads/student_images/no_image.png');
                }
            }
        ?>

        <div class="certificate-page">
            <?php if ($has_custom_bg) { ?>
                <!-- Mode A: Custom Background Layout -->
                <img class="cert-bg-image" src="<?php echo $this->media_storage->getImageURL('uploads/certificate/' . $cert->background_image); ?>" alt="Certificate Background">

                <div class="cert-custom-overlay">
                    <?php if ($cert->enable_student_image == 1 && !empty($student_photo_url)) { ?>
                        <div class="cert-custom-photo" style="top: <?php echo !empty($cert->enable_image_height) ? (int)$cert->enable_image_height : 120; ?>px;">
                            <img src="<?php echo $student_photo_url; ?>" alt="Student Photo">
                        </div>
                    <?php } ?>

                    <?php
                    $content_width = !empty($cert->content_width) ? (int)$cert->content_width : 820;
                    $header_top    = !empty($cert->header_height) ? (int)$cert->header_height : 180;
                    $body_top      = !empty($cert->content_height) ? (int)$cert->content_height : 320;
                    $footer_top    = !empty($cert->footer_height) ? (int)$cert->footer_height : 540;
                    ?>

                    <div class="cert-custom-header" style="width: <?php echo $content_width; ?>px; top: <?php echo $header_top; ?>px;">
                        <div class="hdr-left"><?php echo $parsed_left_hdr; ?></div>
                        <div class="hdr-center"><?php echo $parsed_center_hdr; ?></div>
                        <div class="hdr-right"><?php echo $parsed_right_hdr; ?></div>
                    </div>

                    <div class="cert-custom-body" style="width: <?php echo $content_width; ?>px; top: <?php echo $body_top; ?>px;">
                        <?php echo $parsed_body; ?>
                    </div>

                    <div class="cert-custom-footer" style="width: <?php echo $content_width; ?>px; top: <?php echo $footer_top; ?>px;">
                        <div class="ftr-left"><?php echo $parsed_left_ftr; ?></div>
                        <div class="ftr-center"><?php echo $parsed_center_ftr; ?></div>
                        <div class="ftr-right"><?php echo $parsed_right_ftr; ?></div>
                    </div>
                </div>

            <?php } else { ?>
                <!-- Mode B: Built-in Ornate Professional Certificate Frame -->
                <div class="cert-ornate-container">
                    <div class="cert-outer-border">
                        <div class="corner-accent corner-tl"></div>
                        <div class="corner-accent corner-tr"></div>
                        <div class="corner-accent corner-bl"></div>
                        <div class="corner-accent corner-br"></div>

                        <!-- School Header -->
                        <div class="cert-school-header">
                            <div class="cert-school-name">
                                <?php echo !empty($school_data->name) ? html_escape($school_data->name) : 'ACADEMY OF EXCELLENCE'; ?>
                            </div>
                            <?php if (!empty($school_data->address)) { ?>
                                <div class="cert-school-sub"><?php echo html_escape($school_data->address); ?></div>
                            <?php } ?>

                            <div class="cert-header-meta">
                                <div><?php echo !empty($parsed_left_hdr) ? $parsed_left_hdr : 'Ref No: ' . html_escape($student->admission_no); ?></div>
                                <div><?php echo !empty($parsed_center_hdr) ? $parsed_center_hdr : ''; ?></div>
                                <div><?php echo !empty($parsed_right_hdr) ? $parsed_right_hdr : 'Date: ' . date($this->customlib->getSchoolDateFormat()); ?></div>
                            </div>
                        </div>

                        <!-- Certificate Title -->
                        <div class="cert-title-section">
                            <div class="cert-main-title">
                                <?php echo !empty($cert->certificate_name) ? html_escape($cert->certificate_name) : 'CERTIFICATE OF RECOGNITION'; ?>
                            </div>
                        </div>

                        <!-- Certificate Body -->
                        <div class="cert-content-area">
                            <?php if ($cert->enable_student_image == 1 && !empty($student_photo_url)) { ?>
                                <div class="cert-student-photo-frame">
                                    <img src="<?php echo $student_photo_url; ?>" alt="Student Photo">
                                </div>
                            <?php } ?>

                            <div class="cert-body-paragraph">
                                <?php echo $parsed_body; ?>
                            </div>
                        </div>

                        <!-- Signatures & Footers -->
                        <div class="cert-signatures">
                            <div class="cert-sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title"><?php echo !empty($parsed_left_ftr) ? $parsed_left_ftr : 'Class Teacher'; ?></div>
                            </div>

                            <div class="cert-sig-block">
                                <?php if (!empty($parsed_center_ftr)) { ?>
                                    <div class="sig-line"></div>
                                    <div class="sig-title"><?php echo $parsed_center_ftr; ?></div>
                                <?php } ?>
                            </div>

                            <div class="cert-sig-block">
                                <div class="sig-line"></div>
                                <div class="sig-title"><?php echo !empty($parsed_right_ftr) ? $parsed_right_ftr : 'Principal'; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php } ?>
    </div>

    <script type="text/javascript">
        function triggerPrint() {
            window.print();
        }

        // Wait for all images to fully load before automatically offering print
        window.addEventListener('load', function () {
            var images = document.images;
            var totalImages = images.length;
            var loadedImages = 0;

            if (totalImages === 0) {
                return;
            }

            for (var i = 0; i < totalImages; i++) {
                if (images[i].complete) {
                    loadedImages++;
                } else {
                    images[i].addEventListener('load', checkAllImages);
                    images[i].addEventListener('error', checkAllImages);
                }
            }

            if (loadedImages === totalImages) {
                // All images cached / loaded
            }

            function checkAllImages() {
                loadedImages++;
                if (loadedImages === totalImages) {
                    // Ready
                }
            }
        });
    </script>
</body>
</html>
