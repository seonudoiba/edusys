<?php
/*
 * The CBT exam screen (user/onlineexam/take/<id>). Everything after the first load goes through
 * JSON: start (questions + saved answers + seconds left), answer (autosave), upload, submit.
 * The server keeps the deadline; this page only shows it. Answers not yet confirmed by the
 * server are kept in this browser and sent again, so a lost connection loses nothing.
 */
$brand = $this->customlib->brand();
$brand_color = $brand['color'];
$student_name = $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?php echo html_escape($exam['exam']); ?> · CBT</title>
<link href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo(); ?>" rel="shortcut icon">
<style>
:root{--bg:#eef2f7;--card:#fff;--ink:#14202f;--soft:#5d6878;--rule:#e1e6ee;--brand:<?php echo html_escape($brand_color); ?>;--brand-ink:#fff;--brand-soft:color-mix(in srgb,var(--brand) 9%,#fff);--ok:#138a4a;--ok-bg:#e2f5ea;--flag:#b46900;--flag-bg:#fff2d9;--danger:#b42318;--danger-bg:#fdecea;--radius:14px;--shadow:0 1px 2px rgba(16,24,40,.05),0 6px 20px rgba(16,24,40,.06)}
*{box-sizing:border-box}
html,body{margin:0;height:100%}
body{font:16px/1.55 "Inter","Segoe UI",-apple-system,Roboto,Helvetica,Arial,sans-serif;background:var(--bg);background-image:radial-gradient(1200px 400px at 10% -10%,var(--brand-soft),transparent 60%);background-attachment:fixed;color:var(--ink);-webkit-font-smoothing:antialiased}
button{font:inherit;cursor:pointer}
.top{position:sticky;top:0;z-index:20;background:rgba(255,255,255,.92);backdrop-filter:saturate(1.4) blur(8px);-webkit-backdrop-filter:saturate(1.4) blur(8px);border-bottom:1px solid var(--rule);display:flex;align-items:center;gap:14px;padding:10px 20px;flex-wrap:wrap}
.top img{height:38px;width:auto;border-radius:8px}
.top .who{flex:1;min-width:180px}
.top .who small{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--brand);font-weight:700}
.top .who strong{display:block;font-size:16px;line-height:1.3}
.top .who span{color:var(--soft);font-size:13px}
.timer{font:700 20px/1 ui-monospace,SFMono-Regular,Consolas,monospace;padding:10px 14px;border-radius:12px;background:var(--brand-soft);color:var(--brand);min-width:112px;text-align:center;display:flex;align-items:center;gap:8px;justify-content:center}
.timer::before{content:"";width:9px;height:9px;border-radius:50%;background:currentColor;opacity:.75;animation:pulse 2s infinite}
.timer.low{background:var(--danger-bg);color:var(--danger)}
@keyframes pulse{50%{opacity:.25}}
.savestate{font-size:13px;color:var(--soft);min-width:120px;text-align:right}
.savestate.ok::before{content:"\2713  ";color:var(--ok);font-weight:700}
.savestate.bad{color:var(--danger)}
.savestate.bad::before{content:"!  ";color:var(--danger)}
.progress{height:4px;background:var(--rule);position:sticky;top:0;z-index:19}
.progress span{display:block;height:100%;width:0;background:linear-gradient(90deg,var(--brand),color-mix(in srgb,var(--brand) 55%,#22c55e));transition:width .35s ease}
.wrap{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:20px;max-width:1240px;margin:20px auto;padding:0 20px}
.card{background:var(--card);border:1px solid var(--rule);border-radius:var(--radius);box-shadow:var(--shadow)}
.qcard{padding:26px 30px;min-height:380px;animation:rise .25s ease}
@keyframes rise{from{opacity:.4;transform:translateY(6px)}to{opacity:1;transform:none}}
.qhead{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;color:var(--soft);font-size:14px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px dashed var(--rule)}
.qhead b{color:var(--brand);font-size:15px}
.qhead #qmeta{background:#f3f5f9;border-radius:999px;padding:4px 12px;font-size:13px}
.passage{background:#f8fafc;border:1px solid var(--rule);border-left:4px solid var(--brand);border-radius:10px;padding:14px 18px;margin-bottom:18px;max-height:340px;overflow:auto}
.passage h3{margin:0 0 6px;font-size:15px}
.qtext{font-size:18px;line-height:1.6;margin-bottom:20px;overflow-wrap:anywhere;font-weight:500}
.qtext img,.opt img,.passage img{max-width:100%;height:auto;border-radius:6px}
.opts{display:grid;gap:12px}
.opt{position:relative;display:flex;gap:14px;align-items:center;border:1.5px solid var(--rule);border-radius:12px;padding:13px 16px;background:#fff;transition:border-color .15s,background .15s,box-shadow .15s,transform .1s;cursor:pointer}
.opt:hover{border-color:color-mix(in srgb,var(--brand) 45%,var(--rule));box-shadow:0 2px 10px rgba(16,24,40,.06)}
.opt:active{transform:scale(.995)}
.opt input{position:absolute;opacity:0;width:1px;height:1px}
.opt .letter{flex:none;width:34px;height:34px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;color:var(--soft);background:#f1f4f8;border:1.5px solid var(--rule);transition:all .15s}
.opt .letter:empty::before{content:"";width:12px;height:12px;border-radius:50%;border:2px solid #b3bdcb}
.opt.on{border-color:var(--brand);background:var(--brand-soft);box-shadow:0 0 0 3px color-mix(in srgb,var(--brand) 14%,transparent)}
.opt.on .letter{background:var(--brand);border-color:var(--brand);color:#fff}
.opt.on .letter:empty::before{border-color:#fff;background:#fff}
.opt .body{flex:1;min-width:0;overflow-wrap:anywhere}
.hint{color:var(--soft);font-size:13px;margin:10px 0 0}
textarea{width:100%;min-height:220px;padding:14px 16px;border:1.5px solid var(--rule);border-radius:12px;font:inherit;resize:vertical;background:#fcfdfe}
textarea:focus{border-color:var(--brand);background:#fff}
textarea:focus,.opt:focus-within{outline:3px solid color-mix(in srgb,var(--brand) 30%,transparent);outline-offset:1px}
.filebox{margin-top:14px;font-size:14px;padding:12px 14px;border:1.5px dashed var(--rule);border-radius:12px;background:#fafbfd}
.nav{display:flex;gap:10px;justify-content:space-between;align-items:center;margin-top:24px;flex-wrap:wrap;padding-top:18px;border-top:1px solid var(--rule)}
.btn{border:1.5px solid var(--rule);background:#fff;color:var(--ink);border-radius:10px;padding:10px 18px;min-height:44px;font-weight:600;transition:background .15s,border-color .15s,filter .15s}
.btn:hover{background:#f5f7fa;border-color:#cdd5e0}
.btn.primary{background:var(--brand);border-color:var(--brand);color:var(--brand-ink);box-shadow:0 4px 12px color-mix(in srgb,var(--brand) 30%,transparent)}
.btn.primary:hover{filter:brightness(1.08);background:var(--brand)}
.btn.flag[aria-pressed=true]{background:var(--flag-bg);border-color:#e9b65c;color:var(--flag)}
.btn:disabled{opacity:.45;cursor:default}
.side{padding:18px;align-self:start;position:sticky;top:84px}
.side h2{font-size:14px;margin:0 0 12px;text-transform:uppercase;letter-spacing:.06em;color:var(--soft)}
.ring{display:flex;align-items:center;gap:14px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--rule)}
.ring svg{flex:none}
.ring .big{font-size:22px;font-weight:800;line-height:1}
.ring .small{font-size:12px;color:var(--soft)}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(42px,1fr));gap:7px;max-height:42vh;overflow:auto;padding:3px}
.grid button{height:42px;border:1.5px solid var(--rule);border-radius:10px;background:#fff;font-weight:700;font-size:14px;color:var(--soft);position:relative;transition:all .12s}
.grid button:hover{border-color:var(--brand);color:var(--brand)}
.grid button.answered{background:var(--ok-bg);border-color:#a3d9b9;color:var(--ok)}
.grid button.flagged::after{content:"";position:absolute;top:4px;right:4px;width:8px;height:8px;border-radius:50%;background:var(--flag);box-shadow:0 0 0 2px #fff}
.grid button.current{background:var(--brand);border-color:var(--brand);color:#fff}
.legend{display:grid;grid-template-columns:1fr 1fr;gap:8px 10px;font-size:13px;color:var(--soft);margin:16px 0}
.legend i{display:inline-block;width:12px;height:12px;border-radius:4px;margin-right:6px;vertical-align:-1px;border:1.5px solid var(--rule)}
.legend .a{background:var(--ok-bg);border-color:#a3d9b9}.legend .f{background:var(--flag);border-color:var(--flag)}
.submitbtn{width:100%;padding:13px 18px}
.overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;padding:16px;z-index:50}
.overlay[hidden]{display:none}
.dialog{background:#fff;border-radius:18px;max-width:460px;width:100%;padding:26px;box-shadow:0 24px 60px rgba(15,23,42,.3);animation:rise .2s ease}
.dialog h2{margin:0 0 12px;font-size:20px}
.dialog ul{list-style:none;padding:0;margin:14px 0 18px;display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.dialog li{background:#f5f7fa;border-radius:12px;padding:10px;text-align:center;font-size:12px;color:var(--soft)}
.dialog li b{display:block;font-size:22px;color:var(--ink)}
.dialog .row{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap}
.banner{max-width:1240px;margin:14px auto 0;padding:0 20px}
.banner div{background:var(--danger-bg);color:var(--danger);border:1px solid #f6c8c3;border-radius:12px;padding:12px 16px;font-size:14px;font-weight:500}
.banner[hidden]{display:none}
.loading{max-width:600px;margin:90px auto;text-align:center;color:var(--soft)}
.loading .spin{width:42px;height:42px;border-radius:50%;border:4px solid var(--rule);border-top-color:var(--brand);margin:0 auto 16px;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.togglenav{display:none}
@media (max-width:860px){
  .wrap{grid-template-columns:1fr;padding:0 12px;margin:12px auto 90px}
  .side{position:fixed;left:0;right:0;bottom:0;top:auto;border-radius:18px 18px 0 0;z-index:30;transform:translateY(100%);transition:transform .25s;max-height:78vh;overflow:auto;box-shadow:0 -12px 40px rgba(15,23,42,.2)}
  .side.open{transform:none}
  .togglenav{display:inline-flex}
  .qcard{padding:18px}
  .qtext{font-size:17px}
  .top{padding:8px 12px;gap:10px}
  .timer{font-size:17px;min-width:96px;padding:8px 10px}
  .savestate{width:100%;text-align:left;order:5}
  .nav .btn{flex:1}
}
@media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>
<header class="top">
    <?php if ($brand['logo']) { ?><img src="<?php echo html_escape($brand['logo']); ?>" alt=""><?php } ?>
    <div class="who">
        <small><?php echo html_escape($brand['name']); ?></small>
        <strong><?php echo html_escape($exam['exam']); ?></strong>
        <span><?php echo html_escape($student_name); ?> · <?php echo html_escape($student['admission_no']); ?> · <?php echo html_escape($student['class'] . ' ' . $student['section']); ?></span>
    </div>
    <button type="button" class="btn togglenav" id="togglenav" aria-controls="side" aria-expanded="false">Questions</button>
    <div class="timer" id="timer" role="timer" aria-live="off" title="Time left">--:--</div>
    <div class="savestate" id="savestate" aria-live="polite"></div>
</header>
<div class="progress" aria-hidden="true"><span id="progress"></span></div>
<div class="banner" id="banner" hidden><div id="banner_text"></div></div>

<div class="loading" id="loading">
    <div class="spin" aria-hidden="true"></div>
    <p><?php echo $state === 'running' ? 'Loading your exam where you stopped...' : 'Starting your exam...'; ?></p>
    <p id="loading_error" style="color:var(--danger)"></p>
    <p><a href="<?php echo site_url('user/onlineexam/view/' . (int) $exam['id']); ?>">Back to the exam page</a></p>
</div>

<main class="wrap" id="app" hidden>
    <section class="card qcard" aria-live="polite">
        <div class="qhead"><span id="qnum"></span><span id="qmeta"></span></div>
        <div class="passage" id="passage" hidden></div>
        <div class="qtext" id="qtext"></div>
        <div id="qanswer"></div>
        <div class="nav">
            <button type="button" class="btn" id="prev">&larr; Previous</button>
            <button type="button" class="btn flag" id="flag" aria-pressed="false">Flag for review</button>
            <button type="button" class="btn primary" id="next">Next &rarr;</button>
        </div>
    </section>
    <aside class="card side" id="side" aria-label="Questions">
        <div class="ring">
            <svg width="62" height="62" viewBox="0 0 36 36" aria-hidden="true"><circle cx="18" cy="18" r="15.5" fill="none" stroke="#e6ebf2" stroke-width="4"/><circle id="ring_arc" cx="18" cy="18" r="15.5" fill="none" stroke="var(--brand)" stroke-width="4" stroke-linecap="round" stroke-dasharray="0 100" pathLength="100" transform="rotate(-90 18 18)" style="transition:stroke-dasharray .35s"/></svg>
            <div><div class="big" id="ring_pct">0%</div><div class="small">answered</div></div>
        </div>
        <h2>Questions</h2>
        <div class="grid" id="grid"></div>
        <div class="legend">
            <span><i class="a"></i>Answered <b id="n_answered">0</b></span>
            <span><i></i>Not answered <b id="n_open">0</b></span>
            <span><i class="f"></i>Flagged <b id="n_flagged">0</b></span>
        </div>
        <button type="button" class="btn primary submitbtn" id="review">Submit exam</button>
    </aside>
</main>

<div class="overlay" id="confirm" hidden role="dialog" aria-modal="true" aria-labelledby="confirm_title">
    <div class="dialog">
        <h2 id="confirm_title">Submit your exam?</h2>
        <ul>
            <li><b id="c_answered"></b> answered</li>
            <li><b id="c_open"></b> not answered</li>
            <li><b id="c_flagged"></b> flagged for review</li>
        </ul>
        <p class="hint" id="c_pending" hidden></p>
        <p>You can't change your answers after you submit.</p>
        <div class="row">
            <button type="button" class="btn" id="c_back">Go back</button>
            <button type="button" class="btn primary" id="c_submit">Submit</button>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/plugins/ckeditor_wiris/integration/WIRISplugins.js?viewer=image" defer></script>
<script>
(function () {
    var EXAM = <?php echo (int) $exam['id']; ?>;
    var URL = { start: '<?php echo site_url('user/onlineexam/start'); ?>', answer: '<?php echo site_url('user/onlineexam/answer'); ?>',
        upload: '<?php echo site_url('user/onlineexam/upload'); ?>', submit: '<?php echo site_url('user/onlineexam/submit'); ?>',
        presence: '<?php echo site_url('user/onlineexam/presence'); ?>' };
    var MAX_AWAY = <?php echo (int) (isset($exam['max_tab_switches']) ? $exam['max_tab_switches'] : 0); ?>, BLOCK_COPY = <?php echo !isset($exam['block_copy']) || (int) $exam['block_copy'] ? 'true' : 'false'; ?>;
    var $ = function (id) { return document.getElementById(id); };
    var questions = [], passages = {}, attempt = 0, index = 0, endAt = 0, flags = {}, pending = {}, saving = false, retryTimer = null, finished = false;
    var LETTERS = 'ABCDE';

    function post(url, data) {
        var body = data instanceof FormData ? data : new FormData();
        if (!(data instanceof FormData)) { Object.keys(data).forEach(function (k) { [].concat(data[k]).forEach(function (v) { body.append(Array.isArray(data[k]) ? k + '[]' : k, v); }); }); }
        body.append('exam_id', EXAM);
        return fetch(url, { method: 'POST', body: body, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { if (!r.ok) { throw new Error('HTTP ' + r.status); } return r.json(); });
    }
    function store(key, value) { try { localStorage.setItem('cbt_' + attempt + '_' + key, JSON.stringify(value)); } catch (e) {} }
    function load(key) { try { return JSON.parse(localStorage.getItem('cbt_' + attempt + '_' + key) || 'null'); } catch (e) { return null; } }
    function banner(text) { $('banner_text').textContent = text; $('banner').hidden = !text; }
    function saveState(text, bad) { var s = $('savestate'); s.textContent = text; s.className = 'savestate' + (bad ? ' bad' : (text === 'All answers saved' ? ' ok' : '')); }
    function syncTime(seconds) { if (typeof seconds === 'number') { endAt = Date.now() + seconds * 1000; } }

    // ---- start / resume
    post(URL.start, {}).then(function (d) {
        if (!d.ok) { $('loading_error').textContent = d.error || 'The exam could not be opened.'; return; }
        attempt = d.attempt; questions = d.questions; passages = d.passages || {};
        syncTime(d.seconds_left);
        flags = load('flags') || {};
        // Answers this browser saved but the server never confirmed (connection lost): send them again.
        pending = load('pending') || {};
        questions.forEach(function (q) { if (pending[q.id] !== undefined) { q.answer = pending[q.id]; } });
        index = Math.min(load('index') || 0, questions.length - 1);
        $('loading').hidden = true; $('app').hidden = false;
        buildGrid(); show(index); tick(); setInterval(tick, 500);
        watch();
        if (Object.keys(pending).length) { flush(); } else { saveState('All answers saved'); }
    }).catch(function () { $('loading_error').textContent = 'No connection. Check your internet and reload this page; your time and answers are kept.'; });

    // ---- exam rules: leaving the page is counted (submitted at the limit); copying blocked
    function watch() {
        var awaySince = 0;
        function presence(away) {
            return post(URL.presence, { away: away ? 1 : 0 }).then(function (d) {
                if (d.ended) {
                    finished = true;
                    if (d.forced) { banner('You left the exam page ' + d.count + ' times. Your exam has been submitted.'); }
                    setTimeout(function () { window.location.href = d.redirect || '<?php echo site_url('user/onlineexam'); ?>'; }, d.forced ? 2500 : 0);
                    return;
                }
                syncTime(d.seconds_left);
                if (away && d.max) { banner('Warning: you left the exam page (' + d.count + ' of ' + d.max + '). At ' + d.max + ' your exam is submitted.'); }
                else if (away) { banner('You left the exam page. This is recorded.'); }
            }).catch(function () {});
        }
        document.addEventListener('visibilitychange', function () {
            if (finished) { return; }
            if (document.hidden) { awaySince = Date.now(); }
            else if (awaySince) { awaySince = 0; presence(true); }
        });
        setInterval(function () { if (!finished && !document.hidden) { presence(false); } }, 60000); // extra time given by the invigilator shows
        if (BLOCK_COPY) {
            ['copy', 'cut', 'paste', 'contextmenu', 'dragstart'].forEach(function (ev) {
                document.addEventListener(ev, function (e) { e.preventDefault(); if (ev !== 'contextmenu' && ev !== 'dragstart') { banner('Copying and pasting are turned off for this exam.'); } });
            });
            document.addEventListener('selectstart', function (e) { if (!/^(TEXTAREA|INPUT)$/.test(e.target.tagName) && !e.target.isContentEditable) { e.preventDefault(); } });
            document.addEventListener('keydown', function (e) { if ((e.ctrlKey || e.metaKey) && /^[acxvpsu]$/i.test(e.key) && !/^(TEXTAREA|INPUT)$/.test(e.target.tagName)) { e.preventDefault(); } });
            document.body.style.userSelect = 'none';
        }
    }

    // ---- timer (the server's deadline; refreshed by every save)
    function tick() {
        if (finished) { return; }
        var left = Math.max(0, Math.round((endAt - Date.now()) / 1000));
        var h = Math.floor(left / 3600), m = Math.floor(left % 3600 / 60), s = left % 60;
        $('timer').textContent = (h ? h + ':' + String(m).padStart(2, '0') : m) + ':' + String(s).padStart(2, '0');
        $('timer').className = 'timer' + (left <= 300 ? ' low' : '');
        if (left === 300) { banner('5 minutes left.'); }
        if (left <= 0) { timeUp(); }
    }
    function timeUp() {
        if (finished) { return; }
        finished = true;
        banner('Time is up. Submitting your answers...');
        flush(true).then(submit, submit);
    }

    // ---- navigator
    function answered(q) {
        if (q.type === 'multichoice') { return Array.isArray(q.answer) && q.answer.length > 0; }
        if (q.type === 'descriptive') { return (q.answer && String(q.answer).trim() !== '') || !!q.file; }
        return q.answer !== null && q.answer !== undefined && q.answer !== '';
    }
    function buildGrid() {
        var g = $('grid'); g.innerHTML = '';
        questions.forEach(function (q, i) {
            var b = document.createElement('button');
            b.type = 'button'; b.textContent = i + 1;
            b.addEventListener('click', function () { show(i); closeNav(); });
            g.appendChild(b);
        });
        paintGrid();
    }
    function paintGrid() {
        var a = 0, f = 0;
        [].forEach.call($('grid').children, function (b, i) {
            var q = questions[i], isA = answered(q), isF = !!flags[q.id];
            a += isA ? 1 : 0; f += isF ? 1 : 0;
            b.className = (isA ? 'answered ' : '') + (isF ? 'flagged ' : '') + (i === index ? 'current' : '');
            b.setAttribute('aria-label', 'Question ' + (i + 1) + (isA ? ', answered' : ', not answered') + (isF ? ', flagged' : ''));
            if (i === index) { b.setAttribute('aria-current', 'step'); } else { b.removeAttribute('aria-current'); }
        });
        $('n_answered').textContent = a; $('n_open').textContent = questions.length - a; $('n_flagged').textContent = f;
        var pct = questions.length ? Math.round(a * 100 / questions.length) : 0;
        $('progress').style.width = pct + '%'; $('ring_pct').textContent = pct + '%';
        $('ring_arc').setAttribute('stroke-dasharray', pct + ' 100');
    }
    function closeNav() { $('side').classList.remove('open'); $('togglenav').setAttribute('aria-expanded', 'false'); }
    $('togglenav').addEventListener('click', function () {
        var open = $('side').classList.toggle('open'); this.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // ---- one question
    function show(i) {
        index = Math.max(0, Math.min(i, questions.length - 1)); store('index', index);
        var q = questions[index];
        $('qnum').innerHTML = '<b>Question ' + (index + 1) + '</b> of ' + questions.length;
        $('qmeta').textContent = (q.subject ? q.subject + ' · ' : '') + q.marks + (q.marks === 1 ? ' mark' : ' marks') + (q.neg_marks ? ' · wrong answer -' + q.neg_marks : '');
        var p = q.passage && passages[q.passage];
        $('passage').hidden = !p;
        if (p) { $('passage').innerHTML = '<h3></h3><div></div>'; $('passage').firstChild.textContent = p.title; $('passage').lastChild.innerHTML = p.body; }
        $('qtext').innerHTML = q.html;
        renderAnswer(q);
        var qc = document.querySelector('.qcard'); qc.style.animation = 'none'; void qc.offsetHeight; qc.style.animation = '';
        $('prev').disabled = index === 0;
        $('next').textContent = index === questions.length - 1 ? 'Review and submit' : 'Next →';
        $('flag').setAttribute('aria-pressed', flags[q.id] ? 'true' : 'false');
        $('flag').textContent = flags[q.id] ? 'Flagged' : 'Flag for review';
        paintGrid();
        if (window.com && com.wiris && com.wiris.js && com.wiris.js.JsPluginViewer) { try { com.wiris.js.JsPluginViewer.parseElement(document.querySelector('.qcard'), true); } catch (e) {} }
        window.scrollTo(0, 0);
    }
    function renderAnswer(q) {
        var box = $('qanswer'); box.innerHTML = '';
        if (q.type === 'descriptive') {
            var ta = document.createElement('textarea');
            ta.value = q.answer || ''; ta.setAttribute('aria-label', 'Your answer');
            var count = document.createElement('p'); count.className = 'hint';
            // Words: anything between spaces (counted the same way on the server). At the limit,
            // the text after the last allowed word is cut and the student is told.
            var words = function (cut) {
                var re = /\S+/g, m, n = 0, end = 0;
                while ((m = re.exec(ta.value)) !== null) {
                    n++;
                    if (q.word_limit && n === q.word_limit) { end = m.index + m[0].length; }
                }
                var over = q.word_limit && n > q.word_limit;
                if (over && cut) { ta.value = ta.value.slice(0, end); n = q.word_limit; }
                count.textContent = n + (n === 1 ? ' word' : ' words') + (q.word_limit ? ' of ' + q.word_limit
                    + (n >= q.word_limit ? ' (word limit reached' + (over ? ': the extra words were removed' : '') + ')' : '') : '');
                count.style.color = q.word_limit && n >= q.word_limit ? '#b91c1c' : '';
                return n;
            };
            var t = null;
            ta.addEventListener('input', function () {
                words(true);
                clearTimeout(t);
                t = setTimeout(function () { setAnswer(q, ta.value); }, 800);
            });
            ta.addEventListener('blur', function () { clearTimeout(t); setAnswer(q, ta.value); });
            box.appendChild(ta); box.appendChild(count); words(false);
            var fb = document.createElement('div'); fb.className = 'filebox';
            fb.innerHTML = '<label>Attach a file (optional): <input type="file"></label> <span></span>';
            var info = fb.querySelector('span');
            info.textContent = q.file ? 'Attached: ' + q.file : '';
            fb.querySelector('input').addEventListener('change', function () {
                if (!this.files[0]) { return; }
                var fd = new FormData(); fd.append('question_id', q.id); fd.append('file', this.files[0]);
                info.textContent = 'Uploading...';
                post(URL.upload, fd).then(function (d) {
                    if (d.ended) { return timeUp(); }
                    if (!d.ok) { info.textContent = d.error; return; }
                    q.file = d.file; info.textContent = 'Attached: ' + d.file; syncTime(d.seconds_left); paintGrid();
                }).catch(function () { info.textContent = 'Upload failed. Check your connection and try again.'; });
            });
            box.appendChild(fb);
            return;
        }
        var multi = q.type === 'multichoice', list = document.createElement('div');
        list.className = 'opts'; list.setAttribute('role', multi ? 'group' : 'radiogroup');
        q.options.forEach(function (o, n) {
            var label = document.createElement('label'), input = document.createElement('input');
            input.type = multi ? 'checkbox' : 'radio'; input.name = 'q' + q.id; input.value = o[0];
            input.checked = multi ? (q.answer || []).indexOf(o[0]) > -1 : q.answer === o[0];
            label.className = 'opt' + (input.checked ? ' on' : '');
            var letter = document.createElement('span'); letter.className = 'letter'; letter.textContent = q.type === 'true_false' ? '' : LETTERS[n] + '.';
            var body = document.createElement('span'); body.className = 'body';
            if (q.type === 'true_false') { body.textContent = o[1]; } else { body.innerHTML = o[1]; }
            label.appendChild(input); label.appendChild(letter); label.appendChild(body);
            input.addEventListener('change', function () {
                var value = multi ? [].filter.call(list.querySelectorAll('input'), function (x) { return x.checked; }).map(function (x) { return x.value; }) : this.value;
                [].forEach.call(list.children, function (l) { l.classList.toggle('on', l.querySelector('input').checked); });
                setAnswer(q, value);
            });
            list.appendChild(label);
        });
        box.appendChild(list);
        if (multi) { var h = document.createElement('p'); h.className = 'hint'; h.textContent = 'Choose all the answers that are correct.'; box.appendChild(h); }
        if (!multi && q.answer) {
            var clear = document.createElement('button'); clear.type = 'button'; clear.className = 'btn'; clear.style.marginTop = '10px';
            clear.textContent = 'Clear answer';
            clear.addEventListener('click', function () { setAnswer(q, ''); renderAnswer(q); });
            box.appendChild(clear);
        }
    }

    // ---- autosave
    function setAnswer(q, value) {
        if (finished) { return; }
        if (JSON.stringify(q.answer) === JSON.stringify(value)) { return; }
        q.answer = value; pending[q.id] = value; store('pending', pending); paintGrid();
        flush();
    }
    function flush(force) {
        if ((saving && !force) || !Object.keys(pending).length) { if (!Object.keys(pending).length) { saveState('All answers saved'); } return Promise.resolve(); }
        saving = true; clearTimeout(retryTimer);
        saveState('Saving...');
        var id = Object.keys(pending)[0], value = pending[id];
        var data = { question_id: id };
        data.answer = Array.isArray(value) ? (value.length ? value : '') : value;
        return post(URL.answer, data).then(function (d) {
            saving = false;
            if (d.ended) { finished = true; return submit(); }
            if (!d.ok) { banner(d.error); delete pending[id]; store('pending', pending); syncTime(d.seconds_left); return flush(force); }
            banner('');
            if (JSON.stringify(pending[id]) === JSON.stringify(value)) { delete pending[id]; store('pending', pending); }
            syncTime(d.seconds_left);
            return flush(force);
        }).catch(function () {
            saving = false;
            saveState('Not saved: no connection. Retrying...', true);
            if (!force) { retryTimer = setTimeout(flush, 4000); }
            throw new Error('offline');
        });
    }
    window.addEventListener('online', function () { flush(); });
    window.addEventListener('beforeunload', function (e) { if (!finished && Object.keys(pending).length) { e.preventDefault(); e.returnValue = ''; } });

    // ---- buttons and keys
    $('prev').addEventListener('click', function () { show(index - 1); });
    $('next').addEventListener('click', function () { if (index === questions.length - 1) { openConfirm(); } else { show(index + 1); } });
    $('flag').addEventListener('click', function () {
        var q = questions[index]; if (flags[q.id]) { delete flags[q.id]; } else { flags[q.id] = 1; }
        store('flags', flags); show(index);
    });
    $('review').addEventListener('click', openConfirm);
    document.addEventListener('keydown', function (e) {
        if (!$('confirm').hidden || /TEXTAREA|INPUT/.test(document.activeElement.tagName) && document.activeElement.type !== 'radio' && document.activeElement.type !== 'checkbox') { return; }
        if (e.key === 'ArrowRight') { show(index + 1); }
        if (e.key === 'ArrowLeft') { show(index - 1); }
        var n = 'abcde'.indexOf(e.key.toLowerCase());
        if (n > -1) { var inp = $('qanswer').querySelectorAll('input[type=radio],input[type=checkbox]')[n]; if (inp) { inp.click(); } }
    });

    // ---- submit
    function openConfirm() {
        var a = questions.filter(answered).length, f = questions.filter(function (q) { return flags[q.id]; }).length;
        $('c_answered').textContent = a; $('c_open').textContent = questions.length - a; $('c_flagged').textContent = f;
        var p = Object.keys(pending).length;
        $('c_pending').hidden = !p; $('c_pending').textContent = p ? p + ' answer(s) are not saved yet. They will be sent when you submit.' : '';
        $('confirm').hidden = false; $('c_back').focus();
    }
    $('c_back').addEventListener('click', function () { $('confirm').hidden = true; });
    $('c_submit').addEventListener('click', function () {
        this.disabled = true; finished = true; saveState('Submitting...');
        flush(true).then(submit, function () {
            finished = false; $('c_submit').disabled = false; $('confirm').hidden = true;
            banner('Some answers could not be sent. Check your connection and press Submit again.');
        });
    });
    function submit() {
        return post(URL.submit, {}).then(function (d) {
            try { ['flags', 'pending', 'index'].forEach(function (k) { localStorage.removeItem('cbt_' + attempt + '_' + k); }); } catch (e) {}
            window.location.href = d.redirect || '<?php echo site_url('user/onlineexam'); ?>';
        }).catch(function () { banner('Could not reach the school to submit. Your saved answers are kept; reload this page when you are back online.'); });
    }
})();
</script>
</body>
</html>
