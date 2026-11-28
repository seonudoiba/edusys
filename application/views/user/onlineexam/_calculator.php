<?php
/*
 * The calculator of the CBT exam screen (onlineexam.calculator): a button in the top bar opens it in
 * the page (using it is not leaving the exam). + − × ÷, brackets, %, √, ± and the keyboard while it
 * is open. It works sums out itself: what is typed is never run as code.
 */
?>
<style>
.calc-btn{border:1px solid var(--rule,#e2e8f0);background:#fff;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
.calc-btn[aria-expanded="true"]{background:#0f172a;color:#fff;border-color:#0f172a}
.calc{position:fixed;right:16px;bottom:16px;z-index:40;width:264px;background:#0f172a;border-radius:18px;padding:12px;box-shadow:0 24px 60px -20px rgba(15,23,42,.6);color:#fff;font-family:inherit}
.calc[hidden]{display:none}
.calc-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;font-size:12px;color:#94a3b8;font-weight:700;letter-spacing:.06em;text-transform:uppercase}
.calc-head button{background:none;border:0;color:#94a3b8;font-size:20px;line-height:1;cursor:pointer;padding:0 4px}
.calc-screen{background:#1e293b;border-radius:12px;padding:8px 12px;margin-bottom:10px;text-align:right;min-height:64px}
.calc-expr{font-size:13px;color:#94a3b8;min-height:18px;word-break:break-all}
.calc-out{font-size:26px;font-weight:800;font-variant-numeric:tabular-nums;word-break:break-all}
.calc-keys{display:grid;grid-template-columns:repeat(4,1fr);gap:6px}
.calc-keys button{border:0;border-radius:10px;padding:12px 0;font-size:17px;font-weight:700;background:#334155;color:#fff;cursor:pointer}
.calc-keys button:active{transform:scale(.96)}
.calc-keys .op{background:#475569}
.calc-keys .eq{background:#16a34a}
.calc-keys .clr{background:#b91c1c}
@media (max-width:600px){.calc{left:8px;right:8px;bottom:8px;width:auto}}
</style>
<div class="calc" id="calc" hidden role="dialog" aria-label="Calculator">
    <div class="calc-head" title="Drag to move"><span>Calculator · drag to move</span><button type="button" id="calc_close" aria-label="Close the calculator">&times;</button></div>
    <div class="calc-screen"><div class="calc-expr" id="calc_expr"></div><div class="calc-out" id="calc_out" aria-live="polite">0</div></div>
    <div class="calc-keys">
        <button type="button" class="clr" data-k="C">C</button><button type="button" class="op" data-k="(">(</button><button type="button" class="op" data-k=")">)</button><button type="button" class="op" data-k="/">÷</button>
        <button type="button" data-k="7">7</button><button type="button" data-k="8">8</button><button type="button" data-k="9">9</button><button type="button" class="op" data-k="*">×</button>
        <button type="button" data-k="4">4</button><button type="button" data-k="5">5</button><button type="button" data-k="6">6</button><button type="button" class="op" data-k="-">−</button>
        <button type="button" data-k="1">1</button><button type="button" data-k="2">2</button><button type="button" data-k="3">3</button><button type="button" class="op" data-k="+">+</button>
        <button type="button" class="op" data-k="√">√</button><button type="button" data-k="0">0</button><button type="button" data-k=".">.</button><button type="button" class="op" data-k="%">%</button>
        <button type="button" class="op" data-k="±">±</button><button type="button" class="op" data-k="B" aria-label="Delete">⌫</button><button type="button" class="eq" data-k="=" style="grid-column:span 2">=</button>
    </div>
</div>
<script>
(function () {
    var box = document.getElementById('calc'), btn = document.getElementById('calc_toggle'), outEl = document.getElementById('calc_out'), exprEl = document.getElementById('calc_expr');
    if (!box || !btn) { return; }
    var expr = '', shown = '0';

    // A small parser for + - * / % ( ) √ and unary minus: numbers in, a number (or null) out.
    function evaluate(s) {
        var i = 0;
        function peek() { return s[i]; }
        function num() {
            var m = /^\d*\.?\d+(?:e[+-]?\d+)?|^\d+\.?/i.exec(s.slice(i));
            if (!m) { throw 0; }
            i += m[0].length;
            return parseFloat(m[0]);
        }
        function factor() {
            var c = peek();
            if (c === '-') { i++; return -factor(); }
            if (c === '+') { i++; return factor(); }
            if (c === '√') { i++; var v = factor(); if (v < 0) { throw 0; } return Math.sqrt(v); }
            var x;
            if (c === '(') { i++; x = sum(); if (peek() === ')') { i++; } }
            else { x = num(); }
            while (peek() === '%') { i++; x = x / 100; }
            return x;
        }
        function product() {
            var x = factor();
            while (peek() === '*' || peek() === '/' || peek() === '(' || peek() === '√') {
                var op = peek();
                if (op === '(' || op === '√') { x *= factor(); continue; } // 2(3) and 2√9
                i++;
                var y = factor();
                if (op === '/') { if (y === 0) { throw 0; } x /= y; } else { x *= y; }
            }
            return x;
        }
        function sum() {
            var x = product();
            while (peek() === '+' || peek() === '-') { var op = s[i++]; var y = product(); x = op === '+' ? x + y : x - y; }
            return x;
        }
        try { var r = sum(); return i === s.length && isFinite(r) ? r : null; } catch (e) { return null; }
    }
    function fmt(n) {
        if (n === null) { return 'Error'; }
        var r = Math.round(n * 1e10) / 1e10;
        return Math.abs(r) >= 1e12 || (Math.abs(r) < 1e-6 && r !== 0) ? r.toExponential(6) : String(r);
    }
    function pretty(s) { return s.replace(/\*/g, '×').replace(/\//g, '÷').replace(/-/g, '−'); }
    function draw() { exprEl.textContent = pretty(expr); outEl.textContent = shown; }
    function press(k) {
        if (k === 'C') { expr = ''; shown = '0'; }
        else if (k === 'B') { expr = expr.slice(0, -1); }
        else if (k === '=') { var r = evaluate(expr); shown = fmt(r); if (r !== null) { expr = String(Math.round(r * 1e10) / 1e10); } }
        else if (k === '±') { expr = expr === '' ? '-' : (expr[0] === '-' ? expr.slice(1) : '-(' + expr + ')'); }
        else if (expr.length < 80) { expr += k; }
        if (k !== '=') { var live = evaluate(expr); shown = expr === '' ? '0' : (live === null ? shown : fmt(live)); }
        draw();
    }
    function open(on) {
        box.hidden = !on;
        btn.setAttribute('aria-expanded', on ? 'true' : 'false');
    }
    // drag it by its title bar, out of the way of the question or the Submit button
    var head = box.querySelector('.calc-head'), drag = null;
    head.style.cursor = 'move';
    head.style.touchAction = 'none';
    head.addEventListener('pointerdown', function (e) {
        if (e.target.closest('button')) { return; }
        var r = box.getBoundingClientRect();
        drag = { dx: e.clientX - r.left, dy: e.clientY - r.top };
        head.setPointerCapture(e.pointerId);
    });
    head.addEventListener('pointermove', function (e) {
        if (!drag) { return; }
        var x = Math.max(0, Math.min(window.innerWidth - box.offsetWidth, e.clientX - drag.dx));
        var y = Math.max(0, Math.min(window.innerHeight - box.offsetHeight, e.clientY - drag.dy));
        box.style.left = x + 'px'; box.style.top = y + 'px'; box.style.right = 'auto'; box.style.bottom = 'auto';
    });
    head.addEventListener('pointerup', function () { drag = null; });
    btn.addEventListener('click', function () { open(box.hidden); });
    document.getElementById('calc_close').addEventListener('click', function () { open(false); });
    box.addEventListener('click', function (e) { var b = e.target.closest('button[data-k]'); if (b) { press(b.getAttribute('data-k')); } });
    // the keyboard works the calculator only while it is open and no answer box is being typed in
    document.addEventListener('keydown', function (e) {
        if (box.hidden || e.ctrlKey || e.metaKey || e.altKey) { return; }
        var t = e.target; if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable) && !box.contains(t)) { return; }
        var k = e.key, map = { Enter: '=', '=': '=', Backspace: 'B', Delete: 'C', Escape: null, x: '*', X: '*' };
        if (k === 'Escape') { open(false); e.preventDefault(); return; }
        if (map[k] !== undefined) { k = map[k]; } else if (!/^[0-9.+\-*/()%]$/.test(k)) { return; }
        e.preventDefault();
        press(k);
    });
})();
</script>
