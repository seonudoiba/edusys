<?php
$card = $idcard;
$is_vertical = !empty($card->enable_vertical_card);
$header_bg = !empty($card->header_color) ? $card->header_color : '#1e3a8a';
$school_name = !empty($card->school_name) ? $card->school_name : 'SCHOOL OF EXCELLENCE';
$school_addr = !empty($card->school_address) ? $card->school_address : '110 Kings Street, CA | Phone: (555) 019-2834';
$logo_url = !empty($card->logo) ? $this->media_storage->getImageURL('uploads/student_id_card/logo/' . $card->logo) : '';
$bg_url   = !empty($card->background) ? $this->media_storage->getImageURL('uploads/student_id_card/background/' . $card->background) : '';
$sign_url = !empty($card->sign_image) ? $this->media_storage->getImageURL('uploads/student_id_card/signature/' . $card->sign_image) : '';
$photo_url = $this->media_storage->getImageURL('uploads/student_images/no_image.png');
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">

<style type="text/css">
    .idcard-preview-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: #f8fafc;
        font-family: 'Montserrat', Arial, Helvetica, sans-serif;
    }

    /* Vertical Preview Card */
    .idcard-vert-prev {
        width: 65mm;
        min-height: 102mm;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .idcard-vert-prev-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.12;
        z-index: 1;
    }

    .idcard-vert-prev-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .idcard-vert-prev-header {
        padding: 8px 6px 6px;
        text-align: center;
        color: #ffffff;
    }

    .idcard-vert-prev-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .idcard-vert-prev-logo img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }

    .idcard-vert-prev-schname {
        font-size: 10pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        line-height: 1.1;
    }

    .idcard-vert-prev-schaddr {
        font-size: 6.5pt;
        opacity: 0.9;
        margin-top: 3px;
        line-height: 1.2;
    }

    .idcard-vert-prev-title-bar {
        font-size: 7.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
        padding: 3px 0;
        color: #ffffff;
    }

    .idcard-vert-prev-body {
        padding: 8px 10px;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .idcard-vert-prev-photo {
        width: 65px;
        height: 75px;
        border-radius: 4px;
        overflow: hidden;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        margin: 0 auto 6px;
        background: #f1f5f9;
    }

    .idcard-vert-prev-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .idcard-vert-prev-name {
        font-size: 9.5pt;
        font-weight: 700;
        text-transform: uppercase;
        color: #0f172a;
        margin-bottom: 6px;
        line-height: 1.1;
    }

    .idcard-vert-prev-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7pt;
    }

    .idcard-vert-prev-table td {
        padding: 2px 0;
        vertical-align: top;
        text-align: left;
    }

    .idcard-vert-prev-table td.lbl {
        color: #64748b;
        font-weight: 600;
        width: 44%;
        text-transform: uppercase;
        font-size: 6.5pt;
    }

    .idcard-vert-prev-table td.val {
        color: #0f172a;
        font-weight: 700;
        width: 56%;
    }

    .idcard-vert-prev-footer {
        padding: 4px 10px 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .idcard-vert-prev-barcode {
        max-width: 80px;
        font-size: 6pt;
        color: #64748b;
    }

    .idcard-vert-prev-sign {
        text-align: right;
        max-width: 80px;
    }

    .idcard-vert-prev-sign img {
        max-width: 65px;
        height: 20px;
        object-fit: contain;
        display: block;
        margin-left: auto;
    }

    .idcard-vert-prev-signtitle {
        font-size: 5.5pt;
        color: #475569;
        text-transform: uppercase;
        font-weight: 600;
        border-top: 0.5px solid #94a3b8;
        margin-top: 2px;
        padding-top: 1px;
    }

    /* Horizontal Preview Card */
    .idcard-horiz-prev {
        width: 100mm;
        min-height: 64mm;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .idcard-horiz-prev-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.12;
        z-index: 1;
    }

    .idcard-horiz-prev-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .idcard-horiz-prev-header {
        padding: 6px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #ffffff;
    }

    .idcard-horiz-prev-hdr-left {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }

    .idcard-horiz-prev-hdr-left img {
        width: 28px;
        height: 28px;
        object-fit: contain;
    }

    .idcard-horiz-prev-schname {
        font-size: 9.5pt;
        font-weight: 700;
        text-transform: uppercase;
        line-height: 1.1;
    }

    .idcard-horiz-prev-schaddr {
        font-size: 6pt;
        opacity: 0.9;
        margin-top: 1px;
    }

    .idcard-horiz-prev-title-badge {
        font-size: 6.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(0, 0, 0, 0.25);
        padding: 3px 8px;
        border-radius: 3px;
    }

    .idcard-horiz-prev-body {
        padding: 8px 12px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex: 1;
    }

    .idcard-horiz-prev-photo-col {
        width: 65px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .idcard-horiz-prev-photo {
        width: 62px;
        height: 72px;
        border-radius: 4px;
        overflow: hidden;
        border: 2px solid #ffffff;
        box-shadow: 0 1px 5px rgba(0,0,0,0.18);
        background: #f1f5f9;
    }

    .idcard-horiz-prev-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .idcard-horiz-prev-info-col {
        flex: 1;
    }

    .idcard-horiz-prev-name {
        font-size: 9pt;
        font-weight: 700;
        text-transform: uppercase;
        color: #0f172a;
        margin-bottom: 4px;
        line-height: 1.1;
    }

    .idcard-horiz-prev-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7pt;
    }

    .idcard-horiz-prev-table td {
        padding: 1.5px 0;
        vertical-align: top;
    }

    .idcard-horiz-prev-table td.lbl {
        color: #64748b;
        font-weight: 600;
        width: 40%;
        text-transform: uppercase;
        font-size: 6.2pt;
    }

    .idcard-horiz-prev-table td.val {
        color: #0f172a;
        font-weight: 700;
        width: 60%;
    }

    .idcard-horiz-prev-footer {
        padding: 4px 12px 6px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-top: 0.5px solid #e2e8f0;
        background: #f8fafc;
    }

    .idcard-horiz-prev-sign {
        text-align: right;
    }

    .idcard-horiz-prev-sign img {
        max-width: 60px;
        height: 18px;
        object-fit: contain;
        display: block;
        margin-left: auto;
    }

    .idcard-horiz-prev-signtitle {
        font-size: 5.5pt;
        color: #475569;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 1px;
    }
</style>

<div class="idcard-preview-wrapper">
    <?php if ($is_vertical) { ?>
        <!-- Vertical Preview -->
        <div class="idcard-vert-prev">
            <?php if (!empty($bg_url)) { ?>
                <img class="idcard-vert-prev-bg" src="<?php echo $bg_url; ?>" alt="">
            <?php } ?>

            <div class="idcard-vert-prev-content">
                <div class="idcard-vert-prev-header" style="background: <?php echo $header_bg; ?>;">
                    <div class="idcard-vert-prev-logo">
                        <?php if (!empty($logo_url)) { ?>
                            <img src="<?php echo $logo_url; ?>" alt="Logo">
                        <?php } ?>
                        <div class="idcard-vert-prev-schname"><?php echo html_escape($school_name); ?></div>
                    </div>
                    <?php if (!empty($school_addr)) { ?>
                        <div class="idcard-vert-prev-schaddr"><?php echo html_escape($school_addr); ?></div>
                    <?php } ?>
                </div>

                <div class="idcard-vert-prev-title-bar" style="background: <?php echo $header_bg; ?>; filter: brightness(0.9);">
                    <?php echo !empty($card->title) ? html_escape($card->title) : 'STUDENT IDENTITY CARD'; ?>
                </div>

                <div class="idcard-vert-prev-body">
                    <div class="idcard-vert-prev-photo" style="border-color: <?php echo $header_bg; ?>;">
                        <img src="<?php echo $photo_url; ?>" alt="Sample Student">
                    </div>

                    <?php if ($card->enable_student_name == 1) { ?>
                        <div class="idcard-vert-prev-name">Alexander J. Sterling</div>
                    <?php } ?>

                    <table class="idcard-vert-prev-table">
                        <?php if ($card->enable_admission_no == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('admission_no'); ?>:</td>
                                <td class="val">ADM-2024-089</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_class == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('class'); ?>:</td>
                                <td class="val">Class 10 - A (2024-25)</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_fathers_name == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('father_name'); ?>:</td>
                                <td class="val">Robert Sterling</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_mothers_name == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('mother_name'); ?>:</td>
                                <td class="val">Eleanor Sterling</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_dob == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('d_o_b'); ?>:</td>
                                <td class="val">15-05-2008</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_blood_group == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('blood_group'); ?>:</td>
                                <td class="val" style="color: #b91c1c;">O+</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_phone == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('phone'); ?>:</td>
                                <td class="val">+1 (555) 234-5678</td>
                            </tr>
                        <?php } ?>

                        <?php if ($card->enable_address == 1) { ?>
                            <tr>
                                <td class="lbl"><?php echo $this->lang->line('address'); ?>:</td>
                                <td class="val">742 Evergreen Terrace</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>

                <div class="idcard-vert-prev-footer">
                    <div class="idcard-vert-prev-barcode">
                        <?php if ($card->enable_student_barcode == 1) { ?>
                            <svg width="70" height="20" viewBox="0 0 70 20" xmlns="http://www.w3.org/2000/svg">
                                <rect width="70" height="20" fill="#fff"/>
                                <rect x="2" width="2" height="18" fill="#000"/>
                                <rect x="6" width="1" height="18" fill="#000"/>
                                <rect x="9" width="3" height="18" fill="#000"/>
                                <rect x="14" width="2" height="18" fill="#000"/>
                                <rect x="18" width="1" height="18" fill="#000"/>
                                <rect x="21" width="3" height="18" fill="#000"/>
                                <rect x="26" width="2" height="18" fill="#000"/>
                                <rect x="30" width="1" height="18" fill="#000"/>
                                <rect x="33" width="3" height="18" fill="#000"/>
                                <rect x="38" width="2" height="18" fill="#000"/>
                                <rect x="42" width="1" height="18" fill="#000"/>
                                <rect x="45" width="3" height="18" fill="#000"/>
                                <rect x="50" width="2" height="18" fill="#000"/>
                                <rect x="54" width="2" height="18" fill="#000"/>
                                <rect x="58" width="1" height="18" fill="#000"/>
                                <rect x="62" width="3" height="18" fill="#000"/>
                                <rect x="67" width="1" height="18" fill="#000"/>
                            </svg>
                        <?php } ?>
                    </div>

                    <div class="idcard-vert-prev-sign">
                        <?php if (!empty($sign_url)) { ?>
                            <img src="<?php echo $sign_url; ?>" alt="Signature">
                        <?php } ?>
                        <div class="idcard-vert-prev-signtitle">Principal</div>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { ?>
        <!-- Horizontal Preview -->
        <div class="idcard-horiz-prev">
            <?php if (!empty($bg_url)) { ?>
                <img class="idcard-horiz-prev-bg" src="<?php echo $bg_url; ?>" alt="">
            <?php } ?>

            <div class="idcard-horiz-prev-content">
                <div class="idcard-horiz-prev-header" style="background: <?php echo $header_bg; ?>;">
                    <div class="idcard-horiz-prev-hdr-left">
                        <?php if (!empty($logo_url)) { ?>
                            <img src="<?php echo $logo_url; ?>" alt="Logo">
                        <?php } ?>
                        <div>
                            <div class="idcard-horiz-prev-schname"><?php echo html_escape($school_name); ?></div>
                            <?php if (!empty($school_addr)) { ?>
                                <div class="idcard-horiz-prev-schaddr"><?php echo html_escape($school_addr); ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="idcard-horiz-prev-title-badge">
                        <?php echo !empty($card->title) ? html_escape($card->title) : 'STUDENT ID'; ?>
                    </div>
                </div>

                <div class="idcard-horiz-prev-body">
                    <div class="idcard-horiz-prev-photo-col">
                        <div class="idcard-horiz-prev-photo" style="border-color: <?php echo $header_bg; ?>;">
                            <img src="<?php echo $photo_url; ?>" alt="Student Photo">
                        </div>
                    </div>

                    <div class="idcard-horiz-prev-info-col">
                        <?php if ($card->enable_student_name == 1) { ?>
                            <div class="idcard-horiz-prev-name">Alexander J. Sterling</div>
                        <?php } ?>

                        <table class="idcard-horiz-prev-table">
                            <?php if ($card->enable_admission_no == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('admission_no'); ?>:</td>
                                    <td class="val">ADM-2024-089</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_class == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('class'); ?>:</td>
                                    <td class="val">Class 10 - A (2024-25)</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_fathers_name == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('father_name'); ?>:</td>
                                    <td class="val">Robert Sterling</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_mothers_name == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('mother_name'); ?>:</td>
                                    <td class="val">Eleanor Sterling</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_dob == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('d_o_b'); ?>:</td>
                                    <td class="val">15-05-2008</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_blood_group == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('blood_group'); ?>:</td>
                                    <td class="val" style="color: #b91c1c;">O+</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_phone == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('phone'); ?>:</td>
                                    <td class="val">+1 (555) 234-5678</td>
                                </tr>
                            <?php } ?>

                            <?php if ($card->enable_address == 1) { ?>
                                <tr>
                                    <td class="lbl"><?php echo $this->lang->line('address'); ?>:</td>
                                    <td class="val">742 Evergreen Terrace</td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="idcard-horiz-prev-footer">
                    <div>
                        <?php if ($card->enable_student_barcode == 1) { ?>
                            <svg width="80" height="18" viewBox="0 0 80 18" xmlns="http://www.w3.org/2000/svg">
                                <rect width="80" height="18" fill="#fff"/>
                                <rect x="2" width="2" height="16" fill="#000"/>
                                <rect x="6" width="1" height="16" fill="#000"/>
                                <rect x="9" width="3" height="16" fill="#000"/>
                                <rect x="14" width="2" height="16" fill="#000"/>
                                <rect x="18" width="1" height="16" fill="#000"/>
                                <rect x="22" width="3" height="16" fill="#000"/>
                                <rect x="27" width="2" height="16" fill="#000"/>
                                <rect x="31" width="1" height="16" fill="#000"/>
                                <rect x="35" width="3" height="16" fill="#000"/>
                                <rect x="40" width="2" height="16" fill="#000"/>
                                <rect x="44" width="1" height="16" fill="#000"/>
                                <rect x="48" width="3" height="16" fill="#000"/>
                                <rect x="53" width="2" height="16" fill="#000"/>
                                <rect x="58" width="2" height="16" fill="#000"/>
                                <rect x="62" width="1" height="16" fill="#000"/>
                                <rect x="66" width="3" height="16" fill="#000"/>
                                <rect x="71" width="1" height="16" fill="#000"/>
                                <rect x="75" width="2" height="16" fill="#000"/>
                            </svg>
                        <?php } ?>
                    </div>

                    <div class="idcard-horiz-prev-sign">
                        <?php if (!empty($sign_url)) { ?>
                            <img src="<?php echo $sign_url; ?>" alt="Signature">
                        <?php } ?>
                        <div class="idcard-horiz-prev-signtitle">Principal</div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
