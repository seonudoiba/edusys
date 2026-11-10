<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap"><h1>Find your school.</h1></div>
</section>
<style>
    .fs-box { max-width: 560px; }
    .fs-last { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 16px 18px; margin: 0 0 28px; border: 1px solid var(--rule); border-radius: var(--radius); background: var(--tint); }
    .fs-last[hidden] { display: none; }
    .fs-last div { flex: 1; min-width: 200px; }
    .fs-last strong { display: block; color: var(--ink); }
    .fs-last span { color: var(--faint); font-size: .9rem; }
    .fs-last button { background: none; border: 0; padding: 0; color: var(--soft); text-decoration: underline; cursor: pointer; font: inherit; font-size: .9rem; }
    .fs-field input { width: 100%; font-size: 1.05rem; }
    .fs-status { color: var(--faint); font-size: .9rem; margin: 8px 0 0; min-height: 1.4em; }
    .fs-list { list-style: none; margin: 6px 0 0; padding: 0; border: 1px solid var(--rule); border-radius: var(--radius); overflow: hidden; }
    .fs-list[hidden] { display: none; }
    .fs-list li + li { border-top: 1px solid var(--rule); }
    .fs-list a { display: block; padding: 12px 16px; color: var(--ink); text-decoration: none; }
    .fs-list a:hover, .fs-list a:focus { background: var(--teal-light); outline: none; }
    .fs-list a span { display: block; color: var(--faint); font-size: .88rem; margin-top: 2px; }
    .fs-other { margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--rule); color: var(--soft); }
    .fs-other p { margin: 0 0 10px; }
</style>
<div class="wrap">
    <div class="page-body narrow fs-box">
        <p>Staff, students and parents sign in on their school's own page. Type the name of your school and choose it.</p>

        <div class="fs-last" id="fs_last" hidden>
            <div>
                <span>Last time you used</span>
                <strong id="fs_last_name"></strong>
            </div>
            <a class="btn" id="fs_last_go" href="#">Continue</a>
            <button type="button" id="fs_forget">Not your school?</button>
        </div>

        <div class="field fs-field">
            <label for="fs_q">School name</label>
            <input id="fs_q" type="search" autocomplete="off" spellcheck="false" placeholder="e.g. Greenfield" aria-describedby="fs_status" aria-controls="fs_list">
        </div>
        <p class="fs-status" id="fs_status" role="status" aria-live="polite"></p>
        <ul class="fs-list" id="fs_list" hidden></ul>

        <div class="fs-other">
            <p><strong>School owner?</strong> <a href="<?php echo html_escape(site_url('account')); ?>">Sign in to your subscription</a> to renew it or pay for more students.</p>
            <p><strong>School not listed?</strong> Ask the school for its web address, or <a href="<?php echo html_escape(site_url('plans')); ?>">register your school</a>.</p>
        </div>
    </div>
</div>
<script>
(function () {
    var KEY = 'edusys_school';
    var q = function (id) { return document.getElementById(id); };
    var input = q('fs_q'), list = q('fs_list'), status = q('fs_status');
    var searchUrl = '<?php echo site_url('plans/find_search'); ?>';
    var timer = null, seq = 0;

    function load() {
        try { var s = JSON.parse(localStorage.getItem(KEY) || 'null'); return s && s.name && /^https?:\/\//.test(s.url) ? s : null; } catch (e) { return null; }
    }
    function save(school) {
        try { localStorage.setItem(KEY, JSON.stringify({ name: school.name, url: school.url })); } catch (e) {}
    }

    var last = load();
    if (last) {
        q('fs_last_name').textContent = last.name;
        q('fs_last_go').href = last.url;
        q('fs_last').hidden = false;
    } else {
        input.focus();
    }
    q('fs_forget').addEventListener('click', function () {
        try { localStorage.removeItem(KEY); } catch (e) {}
        q('fs_last').hidden = true;
        input.focus();
    });

    function render(schools, term) {
        list.innerHTML = '';
        if (!schools.length) {
            list.hidden = true;
            status.textContent = 'No school found for "' + term + '". Check the spelling or try part of the name.';
            return;
        }
        schools.forEach(function (s) {
            var li = document.createElement('li'), a = document.createElement('a');
            a.href = s.url;
            a.textContent = s.name;
            if (s.place) {
                var p = document.createElement('span');
                p.textContent = s.place;
                a.appendChild(p);
            }
            a.addEventListener('click', function () { save(s); });
            li.appendChild(a);
            list.appendChild(li);
        });
        list.hidden = false;
        status.textContent = schools.length === 1 ? '1 school found.' : schools.length + ' schools found.';
    }

    function search() {
        var term = input.value.trim(), mine = ++seq;
        if (term.length < 2) {
            list.hidden = true;
            list.innerHTML = '';
            status.textContent = '';
            return;
        }
        status.textContent = 'Searching...';
        fetch(searchUrl + '?q=' + encodeURIComponent(term), { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (mine === seq) { render(d.schools || [], term); } })
            .catch(function () { if (mine === seq) { status.textContent = 'Something went wrong. Please try again.'; } });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(search, 250);
    });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown' && !list.hidden) { e.preventDefault(); list.querySelector('a').focus(); }
        if (e.key === 'Enter') {
            e.preventDefault();
            var first = list.hidden ? null : list.querySelector('a');
            if (first && list.children.length === 1) { first.click(); }
        }
    });
    list.addEventListener('keydown', function (e) {
        var li = document.activeElement.parentNode;
        if (e.key === 'ArrowDown' && li.nextSibling) { e.preventDefault(); li.nextSibling.firstChild.focus(); }
        if (e.key === 'ArrowUp') { e.preventDefault(); (li.previousSibling ? li.previousSibling.firstChild : input).focus(); }
    });
})();
</script>
