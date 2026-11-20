<?php /* Shared styles of the CBT pages and cards (admin/cbtexams, Results > subject). */ ?>
<style>
    .cbt-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px}
    .cbt-tile{display:block;background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:18px;color:#0f172a;transition:transform .15s,box-shadow .15s;position:relative;overflow:hidden}
    .cbt-tile:before{content:"";position:absolute;inset:0 0 auto 0;height:4px;background:#6d28d9}
    .cbt-tile:hover{transform:translateY(-2px);box-shadow:0 16px 32px -18px rgba(15,23,42,.35);color:#0f172a;text-decoration:none}
    .cbt-tile h4{margin:2px 0 4px;font-weight:800}
    .cbt-tile small{color:#64748b}
    .cbt-tile .num{display:inline-block;margin-top:10px;border-radius:99px;padding:3px 10px;background:#ede9fe;color:#6d28d9;font-weight:700;font-size:12px}
    .cbt-muted{color:#64748b}
    .cbt-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:14px}
    .cbt-card{background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:16px 18px}
    .cbt-head{display:flex;gap:10px;justify-content:space-between;align-items:flex-start}
    .cbt-title{font-size:15px}
    .cbt-sub{font-size:12px;color:#64748b;margin-top:2px}
    .cbt-state{border-radius:99px;padding:3px 10px;font-size:11px;font-weight:800;white-space:nowrap}
    .cbt-figs{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin:12px 0}
    .cbt-figs div{background:#f8fafc;border-radius:10px;padding:8px;text-align:center}
    .cbt-figs b{display:block;font-size:17px}
    .cbt-figs small{color:#64748b;font-size:11px}
    .cbt-link{font-size:12.5px;color:#475569;margin-bottom:10px}
    .cbt-actions{display:flex;flex-wrap:wrap;gap:6px;align-items:center}
    details.cbt-pop{display:inline-block;position:relative}
    details.cbt-pop>summary{list-style:none;cursor:pointer}
    details.cbt-pop>summary::-webkit-details-marker{display:none}
    details.cbt-pop[open]>div{position:absolute;z-index:30;top:110%;left:0;background:#fff;border:1px solid #e5e9f0;border-radius:12px;box-shadow:0 18px 40px -18px rgba(15,23,42,.4);padding:12px;min-width:330px}
    .cbt-row{display:flex;gap:8px;margin:8px 0}
    .cbt-row label,.cbt-l{flex:1;font-size:11.5px;font-weight:600;color:#475569;display:block;margin:6px 0}
    .cbt-group{margin:18px 0 10px;font-weight:800;font-size:15px;display:flex;align-items:center;gap:8px}
    .cbt-group span{font-weight:600;font-size:12px;color:#64748b}
</style>
<script>
document.addEventListener('toggle', function (e) {
    var d = e.target; if (!d.matches || !d.matches('details.cbt-pop') || !d.open) { return; }
    document.querySelectorAll('details.cbt-pop[open]').forEach(function (o) { if (o !== d) { o.open = false; } });
}, true);
</script>
