<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| School website themes (Front CMS > Website design). Every theme is a preset of one design: a set of
| options that a school can then change one by one. See libraries/Site_theme.php and
| views/themes/modern/. The keys of the six original themes are kept, so schools keep their choice.
*/

// Fonts a school can choose, loaded from Google Fonts: key => [label, Google Fonts family, CSS stack].
$config['site_theme_fonts'] = array(
    'jakarta' => array('Plus Jakarta Sans', 'Plus+Jakarta+Sans:wght@500;600;700;800', '"Plus Jakarta Sans", "Inter", system-ui, sans-serif'),
    'inter' => array('Inter', 'Inter:wght@400;500;600;700', '"Inter", system-ui, sans-serif'),
    'source_sans' => array('Source Sans 3', 'Source+Sans+3:wght@400;600;700', '"Source Sans 3", system-ui, sans-serif'),
    'lato' => array('Lato', 'Lato:wght@400;700', '"Lato", system-ui, sans-serif'),
    'nunito' => array('Nunito', 'Nunito:wght@400;600;700;800', '"Nunito", system-ui, sans-serif'),
    'poppins' => array('Poppins', 'Poppins:wght@400;500;600;700', '"Poppins", system-ui, sans-serif'),
    'montserrat' => array('Montserrat', 'Montserrat:wght@400;500;600;700', '"Montserrat", system-ui, sans-serif'),
    'work_sans' => array('Work Sans', 'Work+Sans:wght@400;500;600;700', '"Work Sans", system-ui, sans-serif'),
    'dm_sans' => array('DM Sans', 'DM+Sans:wght@400;500;700', '"DM Sans", system-ui, sans-serif'),
    'raleway' => array('Raleway', 'Raleway:wght@400;500;600;700', '"Raleway", system-ui, sans-serif'),
    'fredoka' => array('Fredoka (playful)', 'Fredoka:wght@400;500;600', '"Fredoka", "Nunito", system-ui, sans-serif'),
    'merriweather' => array('Merriweather (serif)', 'Merriweather:wght@400;700', '"Merriweather", Georgia, serif'),
    'playfair' => array('Playfair Display (serif)', 'Playfair+Display:wght@500;600;700', '"Playfair Display", Georgia, serif'),
    'lora' => array('Lora (serif)', 'Lora:wght@400;500;600;700', '"Lora", Georgia, serif'),
    'libre_baskerville' => array('Libre Baskerville (serif)', 'Libre+Baskerville:wght@400;700', '"Libre Baskerville", Georgia, serif'),
    'dm_serif' => array('DM Serif Display (serif)', 'DM+Serif+Display', '"DM Serif Display", Georgia, serif'),
    'fraunces' => array('Fraunces (serif)', 'Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700', '"Fraunces", Georgia, serif'),
);

// What a school can change. Each option: label, type (color|select|check|text), choices or limits, help.
$config['site_theme_options'] = array(
    'primary' => array('label' => 'Main colour', 'type' => 'color', 'group' => 'Colours', 'help' => 'Buttons, links and highlights.'),
    'accent' => array('label' => 'Second colour', 'type' => 'color', 'group' => 'Colours', 'help' => 'Small touches: underlines, badges, the top bar.'),
    'heading_font' => array('label' => 'Heading font', 'type' => 'font', 'group' => 'Text'),
    'body_font' => array('label' => 'Body font', 'type' => 'font', 'group' => 'Text'),
    'text_size' => array('label' => 'Text size', 'type' => 'select', 'group' => 'Text', 'choices' => array('s' => 'Compact', 'm' => 'Standard', 'l' => 'Large')),
    'header' => array('label' => 'Header layout', 'type' => 'select', 'group' => 'Header', 'choices' => array(
        'classic' => 'Logo left, menu right',
        'centered' => 'Logo centred, menu below',
        'stacked' => 'Logo and contacts, menu bar below',
    )),
    'header_style' => array('label' => 'Header colour', 'type' => 'select', 'group' => 'Header', 'choices' => array('light' => 'White', 'brand' => 'Main colour', 'dark' => 'Dark')),
    'menu_style' => array('label' => 'Menu style', 'type' => 'select', 'group' => 'Header', 'choices' => array('underline' => 'Underline', 'pill' => 'Rounded highlight', 'plain' => 'Plain')),
    'topbar' => array('label' => 'Show the top bar (email, phone, social links)', 'type' => 'check', 'group' => 'Header'),
    'sticky' => array('label' => 'Keep the header at the top while scrolling', 'type' => 'check', 'group' => 'Header'),
    'hero' => array('label' => 'Homepage banner', 'type' => 'select', 'group' => 'Homepage banner', 'choices' => array(
        'full' => 'Full width',
        'contained' => 'Within the page',
        'split' => 'Text beside the pictures',
        'none' => 'No banner',
    )),
    'hero_text' => array('label' => 'Show the school name and a call to action on the banner', 'type' => 'check', 'group' => 'Homepage banner'),
    'tagline' => array('label' => 'Banner sentence', 'type' => 'text', 'group' => 'Homepage banner', 'max' => 140, 'help' => 'For example: "Learning with purpose since 1998". Leave empty for none.'),
    'corners' => array('label' => 'Corners', 'type' => 'select', 'group' => 'Shape', 'choices' => array('square' => 'Square', 'soft' => 'Slightly rounded', 'round' => 'Rounded')),
    'buttons' => array('label' => 'Buttons', 'type' => 'select', 'group' => 'Shape', 'choices' => array('solid' => 'Filled', 'outline' => 'Outlined', 'pill' => 'Filled, pill shaped')),
    'width' => array('label' => 'Page width', 'type' => 'select', 'group' => 'Shape', 'choices' => array('standard' => 'Standard', 'wide' => 'Wide')),
    'page_titles' => array('label' => 'Show a title band at the top of inner pages', 'type' => 'check', 'group' => 'Pages'),
    'footer' => array('label' => 'Footer colour', 'type' => 'select', 'group' => 'Footer', 'choices' => array('dark' => 'Dark', 'brand' => 'Main colour', 'light' => 'Light')),
);

// Defaults every preset starts from.
$base = array(
    'primary' => '#1e4f8c', 'accent' => '#d9a21b', 'heading_font' => 'inter', 'body_font' => 'inter', 'text_size' => 'm',
    'header' => 'classic', 'header_style' => 'light', 'menu_style' => 'underline', 'topbar' => 1, 'sticky' => 1,
    'hero' => 'full', 'hero_text' => 1, 'tagline' => '', 'corners' => 'soft', 'buttons' => 'solid', 'width' => 'standard',
    'page_titles' => 1, 'footer' => 'dark',
);
$preset = function ($label, $description, array $options) use ($base) {
    return array('label' => $label, 'description' => $description, 'options' => array_merge($base, $options));
};

$config['site_themes'] = array(
    // The six original themes, redesigned (same keys, so schools keep their choice).
    'default' => $preset('Classic Blue', 'Clear and dependable: a confident blue with warm gold.', array(
        'primary' => '#1e4f8c', 'accent' => '#d9a21b', 'heading_font' => 'source_sans', 'body_font' => 'source_sans', 'header' => 'stacked',
    )),
    'material_pink' => $preset('Rose', 'Warm and welcoming: deep rose with a soft pink touch.', array(
        'primary' => '#b0185a', 'accent' => '#f4a3c2', 'heading_font' => 'poppins', 'body_font' => 'poppins', 'corners' => 'round', 'menu_style' => 'pill',
    )),
    'bold_blue' => $preset('Bold Blue', 'Strong and modern: a bright blue header and bold type.', array(
        'primary' => '#1d4ed8', 'accent' => '#facc15', 'heading_font' => 'montserrat', 'body_font' => 'inter', 'header_style' => 'brand', 'footer' => 'brand', 'buttons' => 'pill',
    )),
    'darkgray' => $preset('Charcoal', 'Serious and refined: charcoal with an amber accent.', array(
        'primary' => '#27303b', 'accent' => '#f59e0b', 'heading_font' => 'work_sans', 'body_font' => 'work_sans', 'header_style' => 'dark', 'corners' => 'square',
    )),
    'shadow_white' => $preset('Paper White', 'Minimal and calm: lots of white, black type, one colour.', array(
        'primary' => '#111827', 'accent' => '#2563eb', 'heading_font' => 'dm_serif', 'body_font' => 'dm_sans', 'header' => 'centered', 'footer' => 'light',
        'corners' => 'square', 'buttons' => 'outline', 'menu_style' => 'plain', 'topbar' => 0,
    )),
    'yellow' => $preset('Sunshine', 'Bright and cheerful: golden yellow with deep brown.', array(
        'primary' => '#9a5b00', 'accent' => '#fbbf24', 'heading_font' => 'nunito', 'body_font' => 'nunito', 'corners' => 'round', 'buttons' => 'pill',
    )),

    // New themes
    'scholar' => $preset('Scholar', 'Traditional and distinguished: burgundy, ivory and serif headings.', array(
        'primary' => '#7a1f2b', 'accent' => '#c7a24b', 'heading_font' => 'playfair', 'body_font' => 'lato', 'header' => 'centered', 'menu_style' => 'plain', 'corners' => 'square',
    )),
    'royal' => $preset('Royal', 'Prestigious: deep purple and gold.', array(
        'primary' => '#4c1d95', 'accent' => '#d4a017', 'heading_font' => 'lora', 'body_font' => 'inter', 'header_style' => 'brand', 'hero' => 'contained',
    )),
    'emerald' => $preset('Emerald', 'Calm and established: rich green with gold.', array(
        'primary' => '#065f46', 'accent' => '#c9a227', 'heading_font' => 'libre_baskerville', 'body_font' => 'source_sans', 'header' => 'stacked', 'footer' => 'brand',
    )),
    'fresh' => $preset('Fresh', 'Light and growing: leaf green, friendly rounded shapes.', array(
        'primary' => '#15803d', 'accent' => '#84cc16', 'heading_font' => 'nunito', 'body_font' => 'nunito', 'corners' => 'round', 'buttons' => 'pill', 'menu_style' => 'pill', 'footer' => 'light',
    )),
    'ocean' => $preset('Ocean', 'Clean and modern: teal with a coral accent.', array(
        'primary' => '#0f766e', 'accent' => '#fb7185', 'heading_font' => 'poppins', 'body_font' => 'inter', 'hero' => 'split',
    )),
    'playful' => $preset('Playground', 'For nursery and primary schools: sky blue, orange and round letters.', array(
        'primary' => '#0284c7', 'accent' => '#f97316', 'heading_font' => 'fredoka', 'body_font' => 'nunito', 'corners' => 'round', 'buttons' => 'pill',
        'menu_style' => 'pill', 'text_size' => 'l', 'hero' => 'contained',
    )),
    'crimson' => $preset('Crimson', 'Energetic and proud: strong red with charcoal.', array(
        'primary' => '#b91c1c', 'accent' => '#1f2937', 'heading_font' => 'raleway', 'body_font' => 'inter', 'header' => 'stacked', 'header_style' => 'dark',
    )),
    'earth' => $preset('Terracotta', 'Warm and grounded: terracotta and olive.', array(
        'primary' => '#9a3412', 'accent' => '#65a30d', 'heading_font' => 'fraunces', 'body_font' => 'dm_sans', 'hero' => 'split', 'footer' => 'light', 'corners' => 'round',
    )),
    'midnight' => $preset('Midnight', 'Polished and premium: navy, white and a fine gold line.', array(
        'primary' => '#0f2742', 'accent' => '#cfa349', 'heading_font' => 'dm_serif', 'body_font' => 'inter', 'header_style' => 'dark', 'header' => 'centered',
        'menu_style' => 'plain', 'buttons' => 'outline', 'corners' => 'square',
    )),
    'modern' => $preset('Modern', 'Sharp and contemporary: indigo with clean lines.', array(
        'primary' => '#4338ca', 'accent' => '#06b6d4', 'heading_font' => 'inter', 'body_font' => 'inter', 'topbar' => 0, 'footer' => 'light', 'width' => 'wide',
    )),
);
unset($base, $preset);
