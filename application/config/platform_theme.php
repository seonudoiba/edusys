<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| The platform website's design (views/themes/platform, backend/themes/platform/site.css), changed on
| the platform's Front CMS > Website design. See libraries/Platform_theme.php.
| Fonts come from site_theme_fonts (config/site_themes.php).
*/

$config['platform_theme_options'] = array(
    'primary' => array('label' => 'Main colour', 'type' => 'color', 'group' => 'Colours', 'help' => 'The header line, dark sections, the slider and the footer. Light colours are darkened so white text stays readable.'),
    'accent' => array('label' => 'Buttons and links', 'type' => 'color', 'group' => 'Colours'),
    'highlight' => array('label' => 'Highlight', 'type' => 'color', 'group' => 'Colours', 'help' => 'Small labels on dark sections and the "New" badge.'),
    'heading_font' => array('label' => 'Heading font', 'type' => 'font', 'group' => 'Text'),
    'body_font' => array('label' => 'Body font', 'type' => 'font', 'group' => 'Text'),
    'text_size' => array('label' => 'Text size', 'type' => 'select', 'group' => 'Text', 'choices' => array('s' => 'Compact', 'm' => 'Standard', 'l' => 'Large')),
    'header_style' => array('label' => 'Header colour', 'type' => 'select', 'group' => 'Header and footer', 'choices' => array('light' => 'White', 'brand' => 'Main colour')),
    'footer' => array('label' => 'Footer colour', 'type' => 'select', 'group' => 'Header and footer', 'choices' => array('brand' => 'Main colour, dark', 'black' => 'Near black')),
    'corners' => array('label' => 'Corners', 'type' => 'select', 'group' => 'Shape', 'choices' => array('square' => 'Square', 'soft' => 'Rounded', 'round' => 'Very rounded')),
    'buttons' => array('label' => 'Buttons', 'type' => 'select', 'group' => 'Shape', 'choices' => array('square' => 'Square', 'soft' => 'Rounded', 'pill' => 'Pill shaped')),
    'width' => array('label' => 'Page width', 'type' => 'select', 'group' => 'Shape', 'choices' => array('standard' => 'Standard', 'wide' => 'Wide')),
    'slider_height' => array('label' => 'Slider height', 'type' => 'select', 'group' => 'Homepage slider', 'choices' => array('compact' => 'Compact', 'standard' => 'Standard', 'tall' => 'Tall (most of the screen)')),
    'slider_speed' => array('label' => 'Next slide after', 'type' => 'select', 'group' => 'Homepage slider', 'choices' => array('4' => '4 seconds', '6' => '6 seconds', '8' => '8 seconds', '10' => '10 seconds', '0' => 'Only when visitors click')),
    'slider_effect' => array('label' => 'Change effect', 'type' => 'select', 'group' => 'Homepage slider', 'choices' => array('fade' => 'Fade', 'slide' => 'Slide')),
    'slider_overlay' => array('label' => 'Darken background photos', 'type' => 'select', 'group' => 'Homepage slider', 'choices' => array('light' => 'A little', 'medium' => 'Medium', 'strong' => 'A lot')),
);

// What each slide can hold (Platform_theme::clean_slides). Images are uploaded on the design page.
$config['platform_slide_fields'] = array(
    'badge' => array('max' => 20),
    'label' => array('max' => 80),
    'title' => array('max' => 120),
    'text' => array('max' => 320),
    'points' => array('max' => 200),
    'btn1_text' => array('max' => 40),
    'btn1_url' => array('max' => 255),
    'btn2_text' => array('max' => 40),
    'btn2_url' => array('max' => 255),
);
$config['platform_slide_visuals'] = array(
    'dashboard' => 'Sample screen: fees and attendance',
    'fees' => 'Sample screen: fee payments',
    'results' => 'Sample screen: results',
    'photo' => 'Photo beside the text',
    'background' => 'Photo behind the text',
    'none' => 'Text only',
);
$config['platform_max_slides'] = 6;

$base = array(
    'primary' => '#0b2d64', 'accent' => '#017e7a', 'highlight' => '#fbb307', 'heading_font' => 'jakarta', 'body_font' => 'inter', 'text_size' => 'm',
    'header_style' => 'light', 'footer' => 'brand', 'corners' => 'soft', 'buttons' => 'soft', 'width' => 'standard',
    'slider_height' => 'standard', 'slider_speed' => '6', 'slider_effect' => 'fade', 'slider_overlay' => 'medium',
);
$preset = function ($label, $description, array $options) use ($base) {
    return array('label' => $label, 'description' => $description, 'options' => array_merge($base, $options));
};
$config['platform_themes'] = array(
    'edusys' => $preset('EduSYS', 'The EduSYS colours: navy, teal and gold.', array()),
    'midnight' => $preset('Midnight', 'Navy header, pill buttons, bright blue.', array(
        'primary' => '#0f172a', 'accent' => '#2563eb', 'highlight' => '#38bdf8', 'header_style' => 'brand', 'footer' => 'black', 'buttons' => 'pill', 'heading_font' => 'poppins',
    )),
    'emerald' => $preset('Emerald', 'Deep green with gold.', array(
        'primary' => '#064e3b', 'accent' => '#047857', 'highlight' => '#f5c542', 'heading_font' => 'dm_sans', 'body_font' => 'dm_sans',
    )),
    'royal' => $preset('Royal', 'Purple and gold, serif headings.', array(
        'primary' => '#3b0764', 'accent' => '#7c3aed', 'highlight' => '#f59e0b', 'heading_font' => 'lora', 'corners' => 'round',
    )),
    'crimson' => $preset('Crimson', 'Strong red on charcoal.', array(
        'primary' => '#1f2937', 'accent' => '#b91c1c', 'highlight' => '#fbbf24', 'heading_font' => 'montserrat', 'footer' => 'black', 'corners' => 'square', 'buttons' => 'square',
    )),
    'ocean' => $preset('Ocean', 'Deep blue with a warm orange.', array(
        'primary' => '#0c4a6e', 'accent' => '#0369a1', 'highlight' => '#fb923c', 'heading_font' => 'nunito', 'body_font' => 'nunito', 'buttons' => 'pill', 'corners' => 'round',
    )),
);

// The slides a new platform starts with (the old fixed banner, then two more).
$config['platform_default_slides'] = array(
    array(
        'badge' => 'New', 'label' => 'Admission forms, online fees and bank transfers',
        'title' => 'Run your whole school on one system.',
        'text' => 'EduSYS brings admissions, fees, attendance, examinations, staff and parent communication together for Nigerian schools, at your school\'s own web address.',
        'points' => "Your own web address\nOnline fee payment\nTraining for your staff",
        'btn1_text' => 'Register your school', 'btn1_url' => 'subscription/add', 'btn2_text' => 'See pricing', 'btn2_url' => 'plans',
        'visual' => 'dashboard', 'image' => '', 'show_price' => 1,
    ),
    array(
        'badge' => '', 'label' => 'Fees',
        'title' => 'Know who has paid, without the paperwork.',
        'text' => 'Parents pay online through Paystack and other Nigerian gateways, or by bank transfer. Every payment lands against the right student, with a receipt.',
        'points' => "Five payment gateways\nTransfers confirmed from the receipt\nReminders for what is outstanding",
        'btn1_text' => 'Register your school', 'btn1_url' => 'subscription/add', 'btn2_text' => 'See every feature', 'btn2_url' => 'product',
        'visual' => 'fees', 'image' => '', 'show_price' => 0,
    ),
    array(
        'badge' => '', 'label' => 'Results',
        'title' => 'CA, exams and report cards, done for you.',
        'text' => 'Teachers enter CA and exam scores, or send them straight from a CBT. Totals, grades, positions and report cards are worked out for every student.',
        'points' => "CBT with automatic marking\nTerm and cumulative results\nReport cards parents can see",
        'btn1_text' => 'Register your school', 'btn1_url' => 'subscription/add', 'btn2_text' => 'See pricing', 'btn2_url' => 'plans',
        'visual' => 'results', 'image' => '', 'show_price' => 0,
    ),
);
