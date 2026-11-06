<style>
    /* Page builder widgets: neutral styles, colours and fonts come from the school's theme. */
    body { overflow-x: clip; }
    .pw-page { margin-bottom: 30px; }
    .pw-section { padding: 26px 0; }
    .pw-inner { position: relative; }
    .pw-full { width: 100vw; margin-left: calc(50% - 50vw); padding-left: 15px; padding-right: 15px; }
    .pw-full > .pw-inner { max-width: 1140px; margin: 0 auto; }
    .pw-bg-light { background: #f5f7fa; }
    .pw-bg-dark { background: #1f2d3d; color: #fff; }
    .pw-bg-dark h2, .pw-bg-dark h3, .pw-bg-dark h4, .pw-bg-dark a:not(.btn) { color: #fff; }
    .pw-widget { margin-bottom: 18px; }
    .pw-widget > h2 { margin: 0 0 16px; font-size: 26px; }
    .pw-center { text-align: center; }
    .pw-img { max-width: 100%; height: auto; border-radius: 4px; }
    .pw-grid { display: grid; gap: 16px; grid-template-columns: repeat(var(--pw-cols, 3), minmax(0, 1fr)); }
    @media (max-width: 767px) { .pw-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 480px) { .pw-grid { grid-template-columns: minmax(0, 1fr); } }
    .pw-card { display: block; background: #fff; color: inherit; border: 1px solid #e6e9ee; border-radius: 6px; overflow: hidden; height: 100%; }
    .pw-card:hover { box-shadow: 0 2px 10px rgba(0, 0, 0, .08); text-decoration: none; }
    .pw-card img { width: 100%; height: 170px; object-fit: cover; display: block; }
    .pw-card-body { padding: 12px 14px; color: #333; }
    .pw-card-body h4 { margin: 0 0 6px; font-size: 16px; color: #333; }
    .pw-muted { color: #777; font-size: 13px; }
    .pw-bg-dark .pw-muted { color: #c5ccd6; }
    .pw-list { list-style: none; margin: 0; padding: 0; }
    .pw-list li { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(0, 0, 0, .08); }
    .pw-date { flex: 0 0 52px; text-align: center; background: rgba(0, 0, 0, .05); border-radius: 4px; padding: 5px 0; font-size: 11px; text-transform: uppercase; line-height: 1.2; }
    .pw-date b { display: block; font-size: 19px; }
    .pw-stats { display: flex; flex-wrap: wrap; justify-content: center; gap: 16px; }
    .pw-stat { flex: 1 1 150px; max-width: 260px; text-align: center; padding: 14px; }
    .pw-stat b { display: block; font-size: 38px; line-height: 1.1; }
    .pw-person { text-align: center; padding: 12px; }
    .pw-person img { width: 110px; height: 110px; object-fit: cover; border-radius: 50%; margin: 0 auto 8px; display: block; }
    .pw-tags { display: flex; flex-wrap: wrap; gap: 8px; }
    .pw-tag { padding: 6px 14px; background: rgba(0, 0, 0, .05); border-radius: 16px; }
    .pw-cta { text-align: center; padding: 16px 10px; }
    .pw-cta h2 { margin-top: 0; }
    .pw-it { display: flex; flex-wrap: wrap; gap: 24px; align-items: center; }
    .pw-it.pw-right { flex-direction: row-reverse; }
    .pw-it > div { flex: 1 1 280px; min-width: 0; }
    .pw-embed { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 4px; }
    .pw-embed iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
    .pw-map iframe { width: 100%; height: 340px; border: 0; border-radius: 4px; }
    .pw-quote { margin: 0; padding: 16px; background: #fff; color: #333; border: 1px solid #e6e9ee; border-left: 3px solid rgba(0, 0, 0, .25); border-radius: 4px; font-size: 15px; }
    .pw-quote-by { margin-top: 8px; color: #777; font-size: 13px; }
    .pw-faq details { border-bottom: 1px solid rgba(0, 0, 0, .1); padding: 10px 0; }
    .pw-faq summary { cursor: pointer; font-weight: 600; }
    .pw-faq details p { margin: 8px 0 0; }
    .pw-plan { padding: 18px; text-align: center; display: flex; flex-direction: column; height: 100%; background: #fff; color: #333; border: 1px solid #e6e9ee; border-radius: 6px; }
    .pw-plan h3 { margin: 0 0 8px; font-size: 18px; color: #333; }
    .pw-price { font-size: 26px; font-weight: 700; margin-bottom: 10px; }
    .pw-price del { font-size: 14px; font-weight: 400; color: #999; display: block; }
    .pw-plan ul { list-style: none; padding: 0; margin: 0 0 14px; flex: 1; font-size: 14px; }
    .pw-plan li { padding: 5px 0; border-top: 1px solid #f0f0f0; }
    .pw-carousel .item img { width: 100%; max-height: 520px; object-fit: cover; }
    .pw-empty { border: 1px dashed #d9a400; background: #fff8e1; color: #6b5500; padding: 12px; border-radius: 4px; font-size: 13px; }
</style>
