<?php
/*
 * Report card letterhead (mPDF): the school's logo, name, address and contacts, used when the
 * template has no header image of its own. Tables only, so mPDF lays it out the same everywhere.
 */
$lh = $this->customlib->brand();
?>
<table cellpadding="0" cellspacing="0" width="100%" style="border-bottom: 3px solid <?php echo $lh['color']; ?>; margin-bottom: 6px;">
    <tr>
        <?php if ($lh['logo']) { ?>
            <td valign="middle" width="90" style="padding: 0 12px 8px 0;"><img src="<?php echo $lh['logo']; ?>" style="height: 72px; width: auto;" /></td>
        <?php } ?>
        <td valign="middle" style="padding-bottom: 8px; <?php echo $lh['logo'] ? '' : 'text-align: center;'; ?>">
            <div style="font-size: 22px; font-weight: bold; color: <?php echo $lh['color']; ?>; line-height: 26px;"><?php echo html_escape(strtoupper($lh['name'])); ?></div>
            <?php if ($lh['address']) { ?><div style="font-size: 11px; color: #444; line-height: 15px;"><?php echo html_escape($lh['address']); ?></div><?php } ?>
            <?php $lh_c = array_filter(array($lh['phone'] ? 'Tel: ' . $lh['phone'] : '', $lh['email'])); if ($lh_c) { ?>
                <div style="font-size: 11px; color: #444; line-height: 15px;"><?php echo html_escape(implode('   |   ', $lh_c)); ?></div>
            <?php } ?>
        </td>
        <?php if ($lh['logo']) { ?><td width="90"></td><?php } ?>
    </tr>
</table>
