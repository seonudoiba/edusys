<?php
/*
 * The homepage slider. Slides and settings come from Front CMS > Website design (Platform_theme);
 * $pt is set by MY_Controller::load_theme. Needs pf_icon (_icons) and $from_price (the lowest rate, or null).
 */
$CI = &get_instance();
$CI->load->library('Platform_theme');
$design = isset($pt) ? $pt : $CI->platform_theme->design(isset($front_setting) ? $front_setting : null);
$slides = $design['slides'];
$o = $design['options'];
if (!$slides) {
    return;
}
$count = count($slides);
$speed = (int) $o['slider_speed'];
$e = function ($v) { return html_escape((string) $v); };
?>
<section class="hero hero-home has-bg slider slider-<?php echo $e($o['slider_effect']); ?>" id="heroSlider" aria-roledescription="carousel" aria-label="Highlights" data-speed="<?php echo $speed; ?>">
    <div class="slides" aria-live="<?php echo $speed && $count > 1 ? 'off' : 'polite'; ?>">
        <?php foreach ($slides as $i => $s) {
            $visual = $s['visual'];
            $img = $s['image'] !== '' ? base_url($s['image']) : '';
            $has_side = in_array($visual, array('dashboard', 'fees', 'results', 'photo'), true);
            $points = array_filter(array_map('trim', explode("\n", $s['points'])), 'strlen');
            $tag = $i === 0 ? 'h1' : 'h2'; ?>
            <div class="slide<?php echo $i === 0 ? ' on' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo ($i + 1) . ' of ' . $count; ?>"<?php echo $i === 0 ? '' : ' aria-hidden="true" inert'; ?>>
                <?php if ($visual === 'background' && $img) { ?>
                    <div class="slide-bg" style="background-image:url('<?php echo $e($img); ?>')"></div>
                <?php } ?>
                <div class="wrap <?php echo $has_side ? 'hero-grid' : 'slide-text-only'; ?>">
                    <div>
                        <?php if ($s['label'] !== '' || $s['badge'] !== '') { ?>
                            <span class="pill"><?php if ($s['badge'] !== '') { ?><b><?php echo $e($s['badge']); ?></b><?php } ?> <?php echo $e($s['label']); ?></span>
                        <?php } ?>
                        <?php if ($s['title'] !== '') { ?><<?php echo $tag; ?> class="h1"><?php echo $e($s['title']); ?></<?php echo $tag; ?>><?php } ?>
                        <?php if ($s['text'] !== '') { ?><p class="lede"><?php echo $e($s['text']); ?></p><?php } ?>
                        <?php if ($s['btn1_text'] !== '' || $s['btn2_text'] !== '') { ?>
                            <div class="actions">
                                <?php if ($s['btn1_text'] !== '' && $s['btn1_url'] !== '') { ?><a class="btn btn-red" href="<?php echo $e($CI->platform_theme->link($s['btn1_url'])); ?>"><?php echo $e($s['btn1_text']); ?> <?php echo pf_icon('arrow'); ?></a><?php } ?>
                                <?php if ($s['btn2_text'] !== '' && $s['btn2_url'] !== '') { ?><a class="btn btn-ghost" href="<?php echo $e($CI->platform_theme->link($s['btn2_url'])); ?>"><?php echo $e($s['btn2_text']); ?></a><?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($s['show_price'] && !empty($from_price)) { ?><p class="from">From <?php echo $from_price; ?> per student, per term. Implementation and training included in a one-time fee.</p><?php } ?>
                        <?php if ($points) { ?>
                            <ul class="ticks"><?php foreach ($points as $p) { ?><li><?php echo pf_icon('check') . $e($p); ?></li><?php } ?></ul>
                        <?php } ?>
                    </div>
                    <?php if ($visual === 'photo' && $img) { ?>
                        <figure class="slide-photo"><img src="<?php echo $e($img); ?>" alt="" <?php echo $i ? 'loading="lazy"' : ''; ?>></figure>
                    <?php } elseif ($visual === 'dashboard') { ?>
                        <div class="preview" aria-hidden="true">
                            <div class="panel-ui">
                                <div class="ui-head"><span>Fees this term</span><span class="ui-dots"><i></i><i></i><i></i></span></div>
                                <table><tr><th>Student</th><th>Class</th><th>Status</th></tr>
                                    <tr><td>Student A</td><td>JSS 1</td><td><span class="tag tag-paid">Paid</span></td></tr>
                                    <tr><td>Student B</td><td>JSS 2</td><td><span class="tag tag-due">Due</span></td></tr>
                                    <tr><td>Student C</td><td>SS 1</td><td><span class="tag tag-paid">Paid</span></td></tr>
                                </table>
                            </div>
                            <div class="preview-cards">
                                <div class="preview-card preview-fees">
                                    <div class="muted">Term fees collected</div>
                                    <div class="big">78%</div>
                                    <div class="bar"><span style="width:78%"></span></div>
                                </div>
                                <div class="preview-card preview-att">
                                    <div class="muted">Attendance today</div>
                                    <div class="row-mini"><span>Present</span><b>412</b></div>
                                    <div class="row-mini"><span>Absent</span><b>9</b></div>
                                    <div class="row-mini"><span>Late</span><b>14</b></div>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($visual === 'fees') { ?>
                        <div class="panel-ui" aria-hidden="true">
                            <div class="ui-head"><span>JSS 2 &middot; Second term fees</span><span class="ui-dots"><i></i><i></i><i></i></span></div>
                            <table>
                                <tr><th>Student</th><th>Paid by</th><th>Status</th></tr>
                                <tr><td>Adaeze O.</td><td>Paystack</td><td><span class="tag tag-paid">Paid</span></td></tr>
                                <tr><td>Tunde B.</td><td>Bank transfer</td><td><span class="tag tag-new">To confirm</span></td></tr>
                                <tr><td>Ifeoma N.</td><td>Monnify</td><td><span class="tag tag-paid">Paid</span></td></tr>
                                <tr><td>Musa A.</td><td>&mdash;</td><td><span class="tag tag-due">Due</span></td></tr>
                            </table>
                        </div>
                    <?php } elseif ($visual === 'results') { ?>
                        <div class="panel-ui" aria-hidden="true">
                            <div class="ui-head"><span>JSS 1 A &middot; Mathematics</span><span class="ui-dots"><i></i><i></i><i></i></span></div>
                            <table>
                                <tr><th>Student</th><th>CA</th><th>Exam</th><th>Grade</th></tr>
                                <tr><td>Chinedu E.</td><td>34</td><td>52</td><td><span class="tag tag-paid grade">A</span></td></tr>
                                <tr><td>Aisha Y.</td><td>28</td><td>41</td><td><span class="tag tag-new grade">B</span></td></tr>
                                <tr><td>Kemi A.</td><td>31</td><td>47</td><td><span class="tag tag-paid grade">A</span></td></tr>
                                <tr><td>Musa A.</td><td>22</td><td>33</td><td><span class="tag tag-due grade">C</span></td></tr>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if ($count > 1) { ?>
        <div class="slider-nav">
            <div class="wrap">
                <button type="button" class="prev" aria-label="Previous slide"><?php echo pf_icon('arrow'); ?></button>
                <div class="slider-dots">
                    <?php for ($i = 0; $i < $count; $i++) { ?><button type="button" aria-label="Slide <?php echo $i + 1; ?>"<?php echo $i === 0 ? ' aria-current="true"' : ''; ?>></button><?php } ?>
                </div>
                <button type="button" class="next" aria-label="Next slide"><?php echo pf_icon('arrow'); ?></button>
                <?php if ($speed) { ?>
                    <button type="button" class="pause" aria-label="Stop the slides"><span class="pause-ic"><?php echo pf_icon('pause'); ?></span><span class="play-ic"><?php echo pf_icon('play'); ?></span></button>
                <?php } ?>
            </div>
        </div>
        <script>
        (function () {
            var root = document.getElementById('heroSlider');
            var slides = root.querySelectorAll('.slide'), dots = root.querySelectorAll('.slider-dots button');
            var pauseBtn = root.querySelector('.pause'), speed = +root.getAttribute('data-speed') * 1000;
            // Plays on its own unless the visitor presses pause. With reduced motion the slides change without the
            // animation (site.css), so they still advance; the pause button stops them.
            var at = 0, timer = null, stopped = false, hold = false;
            function show(n) {
                n = (n + slides.length) % slides.length;
                for (var i = 0; i < slides.length; i++) {
                    var on = i === n;
                    slides[i].classList.toggle('on', on);
                    slides[i].classList.toggle('before', i < n);
                    if (on) { slides[i].removeAttribute('aria-hidden'); slides[i].removeAttribute('inert'); }
                    else { slides[i].setAttribute('aria-hidden', 'true'); slides[i].setAttribute('inert', ''); }
                    if (on) { dots[i].setAttribute('aria-current', 'true'); } else { dots[i].removeAttribute('aria-current'); }
                }
                at = n;
            }
            function tick() {
                clearTimeout(timer);
                if (speed && !stopped && !hold) { timer = setTimeout(function () { show(at + 1); tick(); }, speed); }
            }
            root.querySelector('.prev').addEventListener('click', function () { show(at - 1); tick(); });
            root.querySelector('.next').addEventListener('click', function () { show(at + 1); tick(); });
            for (var i = 0; i < dots.length; i++) {
                dots[i].addEventListener('click', (function (n) { return function () { show(n); tick(); }; })(i));
            }
            if (pauseBtn) {
                if (stopped) { pauseBtn.classList.add('paused'); pauseBtn.setAttribute('aria-label', 'Play the slides'); }
                pauseBtn.addEventListener('click', function () {
                    stopped = !stopped;
                    pauseBtn.classList.toggle('paused', stopped);
                    pauseBtn.setAttribute('aria-label', stopped ? 'Play the slides' : 'Stop the slides');
                    root.querySelector('.slides').setAttribute('aria-live', stopped ? 'polite' : 'off');
                    tick();
                });
            }
            // Wait while a keyboard user is inside the slider (a mouse click on an arrow or dot doesn't hold it,
            // and resting the pointer on the slider, which fills the first screen, doesn't either).
            root.addEventListener('focusin', function (ev) {
                var kb = true;
                try { kb = ev.target.matches(':focus-visible'); } catch (e) {}
                hold = kb; tick();
            });
            root.addEventListener('focusout', function (ev) { if (!root.contains(ev.relatedTarget)) { hold = false; tick(); } });
            document.addEventListener('visibilitychange', function () { if (!document.hidden) { tick(); } });
            var x0 = null;
            root.addEventListener('touchstart', function (ev) { x0 = ev.touches[0].clientX; }, { passive: true });
            root.addEventListener('touchend', function (ev) {
                if (x0 === null) { return; }
                var dx = ev.changedTouches[0].clientX - x0;
                x0 = null;
                if (Math.abs(dx) > 50) { show(at + (dx < 0 ? 1 : -1)); tick(); }
            });
            tick();
        })();
        </script>
    <?php } ?>
</section>
