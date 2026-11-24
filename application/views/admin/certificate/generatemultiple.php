<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student ID Cards</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">
    <style type="text/css">
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0;
            padding: 0;
        }

        .no-print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            background: #0f172a;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
            z-index: 99999;
        }

        .no-print-bar h3 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            color: #f8fafc;
        }

        .btn-print {
            padding: 7px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            background: #2563eb;
            color: #fff;
            transition: all 0.2s ease;
        }
        .btn-print:hover { background: #1d4ed8; }

        .btn-close {
            padding: 7px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            background: #475569;
            color: #fff;
            margin-left: 8px;
        }
        .btn-close:hover { background: #334155; }

        .cards-print-container {
            padding: 65px 15px 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* -------------------------------------------------------------------------
           VERTICAL ID CARD (54mm x 85.6mm)
        -------------------------------------------------------------------------- */
        .idcard-vert {
            width: 54mm;
            min-height: 85.6mm;
            max-height: 86mm;
            border-radius: 6px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .idcard-vert-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.12;
            z-index: 1;
            pointer-events: none;
        }

        .idcard-vert-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .idcard-vert-header {
            padding: 6px 5px 4px;
            text-align: center;
            color: #ffffff;
        }

        .idcard-vert-logo-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .idcard-vert-logo-title img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .idcard-vert-school-name {
            font-size: 8.5pt;
            font-weight: 700;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .idcard-vert-school-addr {
            font-size: 5.5pt;
            line-height: 1.2;
            opacity: 0.9;
            margin-top: 2px;
        }

        .idcard-vert-title-bar {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: center;
            padding: 2.5px 0;
            color: #ffffff;
        }

        .idcard-vert-body {
            padding: 4px 6px;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .idcard-vert-photo {
            width: 52px;
            height: 60px;
            border-radius: 4px;
            overflow: hidden;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            margin: 0 auto 3px;
            background: #f8fafc;
        }

        .idcard-vert-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .idcard-vert-name {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 3px;
            line-height: 1.1;
        }

        .idcard-vert-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6pt;
            margin-top: 2px;
        }

        .idcard-vert-table td {
            padding: 1px 0;
            vertical-align: top;
            text-align: left;
        }

        .idcard-vert-table td.label-cell {
            color: #64748b;
            font-weight: 600;
            width: 44%;
            text-transform: uppercase;
            font-size: 5.5pt;
        }

        .idcard-vert-table td.value-cell {
            color: #0f172a;
            font-weight: 700;
            width: 56%;
            word-break: break-word;
        }

        .idcard-vert-footer {
            padding: 2px 6px 4px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
        }

        .idcard-vert-barcode {
            max-width: 65px;
            text-align: left;
        }

        .idcard-vert-barcode img {
            max-width: 65px;
            height: 18px;
            object-fit: contain;
            display: block;
        }

        .idcard-vert-sign {
            text-align: right;
            max-width: 70px;
        }

        .idcard-vert-sign img {
            max-width: 55px;
            height: 16px;
            object-fit: contain;
            display: block;
            margin-left: auto;
        }

        .idcard-vert-sign-title {
            font-size: 4.5pt;
            color: #475569;
            text-transform: uppercase;
            font-weight: 600;
            border-top: 0.5px solid #94a3b8;
            margin-top: 1px;
            padding-top: 1px;
        }

        /* -------------------------------------------------------------------------
           HORIZONTAL ID CARD (85.6mm x 54mm)
        -------------------------------------------------------------------------- */
        .idcard-horiz {
            width: 85.6mm;
            min-height: 54mm;
            max-height: 54.5mm;
            border-radius: 6px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .idcard-horiz-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.12;
            z-index: 1;
            pointer-events: none;
        }

        .idcard-horiz-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .idcard-horiz-header {
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #ffffff;
        }

        .idcard-horiz-hdr-left {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
        }

        .idcard-horiz-hdr-left img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .idcard-horiz-school-name {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .idcard-horiz-school-addr {
            font-size: 5pt;
            opacity: 0.9;
            line-height: 1.1;
            margin-top: 1px;
        }

        .idcard-horiz-title-badge {
            font-size: 5.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(0, 0, 0, 0.25);
            padding: 2px 6px;
            border-radius: 3px;
            white-space: nowrap;
        }

        .idcard-horiz-body {
            padding: 4px 8px;
            display: flex;
            gap: 8px;
            align-items: center;
            flex: 1;
        }

        .idcard-horiz-photo-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 52px;
        }

        .idcard-horiz-photo {
            width: 50px;
            height: 58px;
            border-radius: 4px;
            overflow: hidden;
            border: 1.5px solid #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.18);
            background: #f8fafc;
        }

        .idcard-horiz-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .idcard-horiz-info-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .idcard-horiz-name {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 2px;
            line-height: 1.1;
        }

        .idcard-horiz-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 5.8pt;
        }

        .idcard-horiz-table td {
            padding: 0.8px 0;
            vertical-align: top;
        }

        .idcard-horiz-table td.lbl {
            color: #64748b;
            font-weight: 600;
            width: 42%;
            text-transform: uppercase;
            font-size: 5.2pt;
        }

        .idcard-horiz-table td.val {
            color: #0f172a;
            font-weight: 700;
            width: 58%;
            word-break: break-word;
        }

        .idcard-horiz-footer {
            padding: 2px 8px 3px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 0.5px solid #e2e8f0;
            background: #f8fafc;
        }

        .idcard-horiz-barcode img {
            max-width: 80px;
            height: 16px;
            object-fit: contain;
            display: block;
        }

        .idcard-horiz-sign {
            text-align: right;
        }

        .idcard-horiz-sign img {
            max-width: 50px;
            height: 14px;
            object-fit: contain;
            display: block;
            margin-left: auto;
        }

        .idcard-horiz-sign-title {
            font-size: 4.5pt;
            color: #475569;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 1px;
        }

        /* -------------------------------------------------------------------------
           PRINT STYLES
        -------------------------------------------------------------------------- */
        @page {
            size: A4 portrait;
            margin: 8mm 6mm;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .cards-print-container {
                padding: 0 !important;
                margin: 0 !important;
                gap: 3mm !important;
                max-width: 100% !important;
                display: flex !important;
                flex-wrap: wrap !important;
                justify-content: flex-start !important;
            }

            .idcard-vert {
                box-shadow: none !important;
                border: 0.5px dashed #cbd5e1 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 2mm !important;
            }

            .idcard-horiz {
                box-shadow: none !important;
                border: 0.5px dashed #cbd5e1 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 2mm !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <h3>
            <?php echo !empty($id_card[0]->title) ? html_escape($id_card[0]->title) : 'Student ID Cards'; ?>
            (<?php echo count($students); ?> Cards)
        </h3>
        <div>
            <button type="button" class="btn-print" onclick="window.print()">&#128438; Print ID Cards</button>
            <button type="button" class="btn-close" onclick="window.close()">&#10006; Close</button>
        </div>
    </div>

    <div class="cards-print-container">
        <?php
        $card = $id_card[0];
        $is_vertical = !empty($card->enable_vertical_card);
        $header_bg = !empty($card->header_color) ? $card->header_color : '#1e3a8a';
        $school_name = !empty($card->school_name) ? $card->school_name : (!empty($sch_settingdata->name) ? $sch_settingdata->name : 'SCHOOL OF EXCELLENCE');
        $school_addr = !empty($card->school_address) ? $card->school_address : (!empty($sch_settingdata->address) ? $sch_settingdata->address : '');
        $logo_url = !empty($card->logo) ? $this->media_storage->getImageURL('uploads/student_id_card/logo/' . $card->logo) : '';
        $bg_url   = !empty($card->background) ? $this->media_storage->getImageURL('uploads/student_id_card/background/' . $card->background) : '';
        $sign_url = !empty($card->sign_image) ? $this->media_storage->getImageURL('uploads/student_id_card/signature/' . $card->sign_image) : '';
        $session_name = isset($sch_setting[0]['current_session']['session']) ? $sch_setting[0]['current_session']['session'] : '';

        foreach ($students as $student) {
            $full_name = $this->customlib->getFullName(
                $student->firstname,
                $student->middlename,
                $student->lastname,
                isset($sch_settingdata->middlename) ? $sch_settingdata->middlename : 1,
                isset($sch_settingdata->lastname) ? $sch_settingdata->lastname : 1
            );

            // Student Photo
            if (!empty($student->image)) {
                $photo_url = $this->media_storage->getImageURL($student->image);
            } else {
                if (strtolower($student->gender) === 'female') {
                    $photo_url = $this->media_storage->getImageURL('uploads/student_images/default_female.jpg');
                } else {
                    $photo_url = $this->media_storage->getImageURL('uploads/student_images/default_male.jpg');
                }
            }

            // DOB formatting
            $dob_display = '';
            if (!empty($student->dob) && $student->dob !== '0000-00-00') {
                $dob_display = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($student->dob));
            }

            // Barcode URL
            $barcode_url = !empty($student->barcode) ? $this->media_storage->getImageURL($student->barcode) : '';
        ?>

        <?php if ($is_vertical) { ?>
            <!-- Vertical ID Card -->
            <div class="idcard-vert">
                <?php if (!empty($bg_url)) { ?>
                    <img class="idcard-vert-bg" src="<?php echo $bg_url; ?>" alt="">
                <?php } ?>

                <div class="idcard-vert-content">
                    <div class="idcard-vert-header" style="background: <?php echo $header_bg; ?>;">
                        <div class="idcard-vert-logo-title">
                            <?php if (!empty($logo_url)) { ?>
                                <img src="<?php echo $logo_url; ?>" alt="Logo">
                            <?php } ?>
                            <div class="idcard-vert-school-name"><?php echo html_escape($school_name); ?></div>
                        </div>
                        <?php if (!empty($school_addr)) { ?>
                            <div class="idcard-vert-school-addr"><?php echo html_escape($school_addr); ?></div>
                        <?php } ?>
                    </div>

                    <div class="idcard-vert-title-bar" style="background: <?php echo $header_bg; ?>; filter: brightness(0.9);">
                        <?php echo !empty($card->title) ? html_escape($card->title) : 'STUDENT IDENTITY CARD'; ?>
                    </div>

                    <div class="idcard-vert-body">
                        <div class="idcard-vert-photo" style="border-color: <?php echo $header_bg; ?>;">
                            <img src="<?php echo $photo_url; ?>" alt="<?php echo html_escape($full_name); ?>">
                        </div>

                        <?php if ($card->enable_student_name == 1) { ?>
                            <div class="idcard-vert-name"><?php echo html_escape($full_name); ?></div>
                        <?php } ?>

                        <table class="idcard-vert-table">
                            <?php if ($card->enable_admission_no == 1) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('admission_no'); ?>:</td>
                                    <td class="value-cell"><?php echo html_escape($student->admission_no); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_class == 1) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('class'); ?>:</td>
                                    <td class="value-cell">
                                        <?php echo html_escape($student->class . ' - ' . $student->section); ?>
                                        <?php if (!empty($session_name)) { echo ' (' . html_escape($session_name) . ')'; } ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_fathers_name == 1 && !empty($student->father_name)) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('father_name'); ?>:</td>
                                    <td class="value-cell"><?php echo html_escape($student->father_name); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_mothers_name == 1 && !empty($student->mother_name)) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('mother_name'); ?>:</td>
                                    <td class="value-cell"><?php echo html_escape($student->mother_name); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_dob == 1 && !empty($dob_display)) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('d_o_b'); ?>:</td>
                                    <td class="value-cell"><?php echo html_escape($dob_display); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_blood_group == 1 && !empty($student->blood_group)) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('blood_group'); ?>:</td>
                                    <td class="value-cell" style="color: #b91c1c;"><?php echo html_escape($student->blood_group); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_phone == 1 && !empty($student->mobileno)) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('phone'); ?>:</td>
                                    <td class="value-cell"><?php echo html_escape($student->mobileno); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_address == 1 && !empty($student->current_address)) { ?>
                                <tr>
                                    <td class="label-cell"><?php echo $this->lang->line('address'); ?>:</td>
                                    <td class="value-cell"><?php echo html_escape($student->current_address); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>

                    <div class="idcard-vert-footer">
                        <div class="idcard-vert-barcode">
                            <?php if ($card->enable_student_barcode == 1 && !empty($barcode_url)) { ?>
                                <img src="<?php echo $barcode_url; ?>" alt="Barcode">
                            <?php } ?>
                        </div>

                        <div class="idcard-vert-sign">
                            <?php if (!empty($sign_url)) { ?>
                                <img src="<?php echo $sign_url; ?>" alt="Signature">
                            <?php } ?>
                            <div class="idcard-vert-sign-title">Principal</div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } else { ?>
            <!-- Horizontal ID Card -->
            <div class="idcard-horiz">
                <?php if (!empty($bg_url)) { ?>
                    <img class="idcard-horiz-bg" src="<?php echo $bg_url; ?>" alt="">
                <?php } ?>

                <div class="idcard-horiz-content">
                    <div class="idcard-horiz-header" style="background: <?php echo $header_bg; ?>;">
                        <div class="idcard-horiz-hdr-left">
                            <?php if (!empty($logo_url)) { ?>
                                <img src="<?php echo $logo_url; ?>" alt="Logo">
                            <?php } ?>
                            <div>
                                <div class="idcard-horiz-school-name"><?php echo html_escape($school_name); ?></div>
                                <?php if (!empty($school_addr)) { ?>
                                    <div class="idcard-horiz-school-addr"><?php echo html_escape($school_addr); ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="idcard-horiz-title-badge">
                            <?php echo !empty($card->title) ? html_escape($card->title) : 'STUDENT ID'; ?>
                        </div>
                    </div>

                    <div class="idcard-horiz-body">
                        <div class="idcard-horiz-photo-col">
                            <div class="idcard-horiz-photo" style="border-color: <?php echo $header_bg; ?>;">
                                <img src="<?php echo $photo_url; ?>" alt="<?php echo html_escape($full_name); ?>">
                            </div>
                        </div>

                        <div class="idcard-horiz-info-col">
                            <?php if ($card->enable_student_name == 1) { ?>
                                <div class="idcard-horiz-name"><?php echo html_escape($full_name); ?></div>
                            <?php } ?>

                            <table class="idcard-horiz-table">
                                <?php if ($card->enable_admission_no == 1) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('admission_no'); ?>:</td>
                                        <td class="val"><?php echo html_escape($student->admission_no); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_class == 1) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('class'); ?>:</td>
                                        <td class="val">
                                            <?php echo html_escape($student->class . ' - ' . $student->section); ?>
                                            <?php if (!empty($session_name)) { echo ' (' . html_escape($session_name) . ')'; } ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_fathers_name == 1 && !empty($student->father_name)) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('father_name'); ?>:</td>
                                        <td class="val"><?php echo html_escape($student->father_name); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_mothers_name == 1 && !empty($student->mother_name)) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('mother_name'); ?>:</td>
                                        <td class="val"><?php echo html_escape($student->mother_name); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_dob == 1 && !empty($dob_display)) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('d_o_b'); ?>:</td>
                                        <td class="val"><?php echo html_escape($dob_display); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_blood_group == 1 && !empty($student->blood_group)) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('blood_group'); ?>:</td>
                                        <td class="val" style="color: #b91c1c;"><?php echo html_escape($student->blood_group); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_phone == 1 && !empty($student->mobileno)) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('phone'); ?>:</td>
                                        <td class="val"><?php echo html_escape($student->mobileno); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if ($card->enable_address == 1 && !empty($student->current_address)) { ?>
                                    <tr>
                                        <td class="lbl"><?php echo $this->lang->line('address'); ?>:</td>
                                        <td class="val"><?php echo html_escape($student->current_address); ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>

                    <div class="idcard-horiz-footer">
                        <div class="idcard-horiz-barcode">
                            <?php if ($card->enable_student_barcode == 1 && !empty($barcode_url)) { ?>
                                <img src="<?php echo $barcode_url; ?>" alt="Barcode">
                            <?php } ?>
                        </div>

                        <div class="idcard-horiz-sign">
                            <?php if (!empty($sign_url)) { ?>
                                <img src="<?php echo $sign_url; ?>" alt="Signature">
                            <?php } ?>
                            <div class="idcard-horiz-sign-title">Principal</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php } ?>
    </div>
</body>
</html>
