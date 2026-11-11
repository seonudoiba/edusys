<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * A student's fee bill, printable on A4 (libraries/Fee_bill.php). $bill from Fee_bill::build().
 */
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$money = function ($v) { return amountFormat($v); };
$sym = $this->customlib->getSchoolCurrencyFormat();
$s = $bill['student'];
$school = $bill['school'];
$name = $this->customlib->getFullName($s['firstname'], $s['middlename'], $s['lastname'], 1, 1);
$date = function ($d) { return $d ? date($this->customlib->getSchoolDateFormat(), strtotime($d)) : ''; };
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fee bill: <?php echo $e($name); ?></title>
    <style>
        :root { --ink: #1b2430; --soft: #5b6676; --rule: #d9dee6; --head: #f3f5f8; --due: #b42318; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #e9edf2; color: var(--ink); font: 14px/1.45 Arial, Helvetica, sans-serif; }
        .sheet { max-width: 800px; margin: 20px auto; background: #fff; padding: 32px 36px; box-shadow: 0 1px 4px rgba(0,0,0,.12); }
        header { display: flex; justify-content: space-between; gap: 20px; border-bottom: 2px solid var(--ink); padding-bottom: 14px; }
        header h1 { margin: 0; font-size: 20px; }
        header p { margin: 2px 0; color: var(--soft); }
        .doc { text-align: right; }
        .doc b { display: block; font-size: 18px; letter-spacing: .04em; }
        .who { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 6px 20px; margin: 16px 0; }
        .who span { color: var(--soft); display: block; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-variant-numeric: tabular-nums; }
        th { background: var(--head); text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: var(--soft); }
        th, td { padding: 8px 10px; border-bottom: 1px solid var(--rule); vertical-align: top; }
        td.n, th.n { text-align: right; white-space: nowrap; }
        tr.total td { font-weight: bold; border-top: 2px solid var(--ink); border-bottom: 0; }
        .due { color: var(--due); }
        .balance { margin: 18px 0; padding: 14px 16px; border: 2px solid var(--ink); display: flex; justify-content: space-between; align-items: center; font-size: 16px; }
        .balance b { font-size: 22px; }
        .pay { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; }
        .pay div { border: 1px solid var(--rule); padding: 12px 14px; }
        .pay h2 { margin: 0 0 6px; font-size: 14px; }
        .pay p { margin: 2px 0; }
        .foot { margin-top: 18px; color: var(--soft); font-size: 12px; }
        .tools { max-width: 800px; margin: 12px auto 0; text-align: right; }
        .tools button { font: inherit; padding: 8px 14px; cursor: pointer; }
        @media print { body { background: #fff; } .sheet { box-shadow: none; margin: 0; max-width: none; padding: 0; } .tools { display: none; } }
    </style>
</head>
<body>
<div class="tools"><button type="button" onclick="window.print()">Print or save as PDF</button></div>
<div class="sheet">
    <header>
        <div>
            <h1><?php echo $e($school['name'] ?? ''); ?></h1>
            <p><?php echo $e($school['address'] ?? ''); ?></p>
            <p><?php echo $e(trim(($school['phone'] ?? '') . '  ' . ($school['email'] ?? ''))); ?></p>
        </div>
        <div class="doc"><b>FEE BILL</b><?php echo $e($bill['session']); ?> session<br><?php echo $e(date($this->customlib->getSchoolDateFormat())); ?></div>
    </header>

    <div class="who">
        <div><span>Student</span><?php echo $e($name); ?></div>
        <div><span>Admission no.</span><?php echo $e($s['admission_no']); ?></div>
        <div><span>Class</span><?php echo $e($s['class'] . ' ' . $s['section']); ?></div>
        <div><span>Parent / guardian</span><?php echo $e($s['guardian_name']); ?></div>
    </div>

    <table>
        <thead><tr><th>Fee</th><th>Due date</th><th class="n">Amount</th><th class="n">Paid</th><th class="n">Discount</th><th class="n">Balance</th></tr></thead>
        <tbody>
        <?php if (!$bill['rows']) { ?>
            <tr><td colspan="6">No fees have been assigned for this session.</td></tr>
        <?php } ?>
        <?php foreach ($bill['rows'] as $r) { ?>
            <tr>
                <td><?php echo $e($r['group']); ?><br><span style="color:var(--soft)"><?php echo $e($r['type']); ?></span></td>
                <td class="<?php echo $r['overdue'] && $r['balance'] > 0 ? 'due' : ''; ?>"><?php echo $e($date($r['due_date'])); ?><?php echo $r['overdue'] && $r['balance'] > 0 ? ' (overdue)' : ''; ?></td>
                <td class="n"><?php echo $money($r['amount']); ?></td>
                <td class="n"><?php echo $money($r['paid']); ?></td>
                <td class="n"><?php echo $money($r['discount']); ?></td>
                <td class="n"><?php echo $money($r['balance']); ?></td>
            </tr>
        <?php } ?>
        <?php if ($bill['rows']) { ?>
            <tr class="total"><td colspan="2">Total</td>
                <td class="n"><?php echo $money($bill['totals']['amount']); ?></td>
                <td class="n"><?php echo $money($bill['totals']['paid']); ?></td>
                <td class="n"><?php echo $money($bill['totals']['discount']); ?></td>
                <td class="n"><?php echo $money($bill['totals']['balance']); ?></td></tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="balance"><span>Amount to pay</span><b><?php echo $e($sym) . $money($bill['totals']['balance']); ?></b></div>

    <?php if ($bill['totals']['balance'] > 0 && ($bill['bank'] || $bill['online'])) { ?>
        <div class="pay">
            <?php if ($bill['bank']) { ?>
                <div>
                    <h2>Pay by bank transfer</h2>
                    <p><?php echo $e($bill['bank']['bank_name']); ?></p>
                    <p>Account name: <b><?php echo $e($bill['bank']['account_name']); ?></b></p>
                    <p>Account number: <b><?php echo $e($bill['bank']['account_number']); ?></b></p>
                    <p>Use the student's name and admission number as the reference.</p>
                    <?php if ($bill['bank']['note'] !== '') { ?><p><?php echo nl2br($e($bill['bank']['note'])); ?></p><?php } ?>
                </div>
            <?php } ?>
            <?php if ($bill['online']) { ?>
                <div>
                    <h2>Pay online</h2>
                    <p>Sign in to the parent portal and open Fees to pay by card or transfer:</p>
                    <p><b><?php echo $e($bill['portal']); ?></b></p>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <p class="foot">You can pay in parts; every payment is recorded against the fees above. Please keep your receipts.</p>
</div>
</body>
</html>
