<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Blocks for the page editor: the catalogue, cleaning a layout before it is saved, fetching each
 * block's data (always the current school's) and rendering a layout to HTML.
 *
 * A layout is JSON, a tree of blocks:
 *   {"v":2,"blocks":[block, ...]}
 *   block: {"type":"paragraph","attrs":{...}}            content, media and school-data blocks
 *          {"type":"section","attrs":{...},"inner":[...]} a band with a background
 *          {"type":"columns","attrs":{"layout":"1-1"},"inner":[{"type":"column","inner":[...]}, ...]}
 * Sections hold columns or content; columns hold content only. Layouts from the first builder,
 * {"sections":[{"layout","bg","full","cols":[[widget...]]}]}, are converted when read (normalize()).
 *
 * The rendered HTML goes into the page's `description`, so every theme shows it unchanged, and the
 * contact and complaint blocks use the existing [form-builder:...] handling of Welcome::page().
 *
 * Public pages: students appear only as totals; staff only if marked "show on website".
 */
class Page_widgets
{
    const LAYOUTS = array('1' => array(12), '1-1' => array(6, 6), '1-2' => array(4, 8), '2-1' => array(8, 4), '1-1-1' => array(4, 4, 4), '1-1-1-1' => array(3, 3, 3, 3));
    const BACKGROUNDS = array('none' => 'None', 'light' => 'Light', 'dark' => 'Dark');
    const PADDINGS = array('s' => 'Small', 'm' => 'Medium', 'l' => 'Large');
    const CONTAINERS = array('section', 'columns', 'column');
    const MAX_SECTIONS = 40;
    const MAX_WIDGETS = 12; // per column, in old layouts
    const MAX_BLOCKS = 400;  // in a whole page
    const MAX_INNER = 80;    // in one container
    const MAX_JSON = 600000;
    const INLINE_TAGS = array('b', 'strong', 'i', 'em', 'u', 's', 'a', 'br', 'sub', 'sup', 'code');
    const FORMS = array('contact_form' => 'contact_us', 'complaint_form' => 'complain');

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** The catalogue: type => label, icon, group (dynamic|platform|content), fields. */
    public static function catalogue()
    {
        $title = function ($default) { return array('type' => 'text', 'label' => 'Title', 'default' => $default); };
        $count = function ($default, $max = 12) { return array('type' => 'number', 'label' => 'How many to show', 'default' => $default, 'min' => 1, 'max' => $max); };
        $columns = array('type' => 'select', 'label' => 'Per row', 'default' => '3', 'options' => array('2' => '2', '3' => '3', '4' => '4'));
        return array(
            // ------- text (edited directly on the page)
            'paragraph' => array('label' => 'Paragraph', 'icon' => 'fa-paragraph', 'group' => 'text', 'keywords' => 'text body', 'fields' => array(
                'html' => array('type' => 'inline', 'label' => 'Text', 'default' => ''),
                'align' => array('type' => 'select', 'label' => 'Alignment', 'default' => 'left', 'options' => array('left' => 'Left', 'center' => 'Centre', 'right' => 'Right')),
            )),
            'heading' => array('label' => 'Heading', 'icon' => 'fa-header', 'group' => 'text', 'keywords' => 'title h2 h3', 'fields' => array(
                'html' => array('type' => 'inline', 'label' => 'Heading', 'default' => ''),
                'level' => array('type' => 'select', 'label' => 'Size', 'default' => '2', 'options' => array('2' => 'Large (H2)', '3' => 'Medium (H3)', '4' => 'Small (H4)')),
                'align' => array('type' => 'select', 'label' => 'Alignment', 'default' => 'left', 'options' => array('left' => 'Left', 'center' => 'Centre', 'right' => 'Right')),
            )),
            'list' => array('label' => 'List', 'icon' => 'fa-list-ul', 'group' => 'text', 'keywords' => 'bullets numbers', 'fields' => array(
                'html' => array('type' => 'listhtml', 'label' => 'Items', 'default' => ''),
                'ordered' => array('type' => 'check', 'label' => 'Numbered', 'default' => 0),
            )),
            'quote' => array('label' => 'Quote', 'icon' => 'fa-quote-left', 'group' => 'text', 'keywords' => 'blockquote citation', 'fields' => array(
                'html' => array('type' => 'inline', 'label' => 'Quote', 'default' => ''),
                'cite' => array('type' => 'text', 'label' => 'Who said it', 'default' => ''),
            )),
            'table' => array('label' => 'Table', 'icon' => 'fa-table', 'group' => 'text', 'keywords' => 'grid rows', 'fields' => array(
                'rows' => array('type' => 'table', 'label' => 'Cells', 'default' => array(array('', ''), array('', ''))),
                'header' => array('type' => 'check', 'label' => 'First row is a header', 'default' => 1),
            )),
            // ------- media
            'image_gallery' => array('label' => 'Image gallery', 'icon' => 'fa-picture-o', 'group' => 'media', 'keywords' => 'photos pictures', 'fields' => array(
                'images' => array('type' => 'images', 'label' => 'Images', 'default' => array(), 'max' => 24),
                'columns' => array('type' => 'select', 'label' => 'Per row', 'default' => '3', 'options' => array('2' => '2', '3' => '3', '4' => '4')),
            )),
            // ------- design
            'section' => array('label' => 'Section', 'icon' => 'fa-square-o', 'group' => 'design', 'keywords' => 'group band background container', 'fields' => array(
                'bg' => array('type' => 'select', 'label' => 'Background', 'default' => 'none', 'options' => self::BACKGROUNDS),
                'padding' => array('type' => 'select', 'label' => 'Space above and below', 'default' => 'm', 'options' => self::PADDINGS),
                'full' => array('type' => 'check', 'label' => 'Full width background', 'default' => 0),
            )),
            'columns' => array('label' => 'Columns', 'icon' => 'fa-columns', 'group' => 'design', 'keywords' => 'row grid side by side', 'fields' => array(
                'layout' => array('type' => 'select', 'label' => 'Columns', 'default' => '1-1', 'options' => array('1' => '1 column', '1-1' => '2 equal', '1-2' => 'Narrow + wide', '2-1' => 'Wide + narrow', '1-1-1' => '3 equal', '1-1-1-1' => '4 equal')),
            )),
            'column' => array('label' => 'Column', 'icon' => 'fa-square-o', 'group' => 'hidden', 'fields' => array()),
            'separator' => array('label' => 'Separator', 'icon' => 'fa-minus', 'group' => 'design', 'keywords' => 'line divider hr', 'fields' => array()),
            // ------- from the school's data
            'banner' => array('label' => 'Banner slider', 'icon' => 'fa-picture-o', 'group' => 'dynamic', 'hint' => 'Images from Front CMS > Banner Images', 'fields' => array()),
            'news' => array('label' => 'Latest news', 'icon' => 'fa-newspaper-o', 'group' => 'dynamic', 'hint' => 'From Front CMS > News', 'fields' => array('title' => $title('Latest news'), 'count' => $count(4))),
            'events' => array('label' => 'Upcoming events', 'icon' => 'fa-calendar', 'group' => 'dynamic', 'hint' => 'From Front CMS > Events', 'fields' => array('title' => $title('Upcoming events'), 'count' => $count(4))),
            'gallery' => array('label' => 'Gallery', 'icon' => 'fa-th', 'group' => 'dynamic', 'hint' => 'From Front CMS > Gallery', 'fields' => array('title' => $title('Gallery'), 'count' => $count(6), 'columns' => $columns)),
            'stats' => array('label' => 'School stats', 'icon' => 'fa-bar-chart', 'group' => 'dynamic', 'hint' => 'Live totals only, never names', 'fields' => array(
                'students' => array('type' => 'check', 'label' => 'Students', 'default' => 1),
                'staff' => array('type' => 'check', 'label' => 'Staff', 'default' => 1),
                'classes' => array('type' => 'check', 'label' => 'Classes', 'default' => 1),
                'extra' => array('type' => 'items', 'label' => 'Your own numbers', 'default' => array(), 'max' => 6, 'fields' => array(
                    'value' => array('type' => 'text', 'label' => 'Number', 'default' => ''),
                    'label' => array('type' => 'text', 'label' => 'Label', 'default' => ''),
                )),
            )),
            'staff' => array('label' => 'Staff directory', 'icon' => 'fa-users', 'group' => 'dynamic', 'hint' => 'Only staff marked "show on website"', 'fields' => array('title' => $title('Our staff'), 'count' => $count(8, 40), 'columns' => array('default' => '4') + $columns)),
            'classes' => array('label' => 'Classes offered', 'icon' => 'fa-graduation-cap', 'group' => 'dynamic', 'hint' => 'From Academics > Classes', 'fields' => array('title' => $title('Classes we offer'))),
            'admission' => array('label' => 'Admission call-to-action', 'icon' => 'fa-pencil-square-o', 'group' => 'dynamic', 'hint' => 'Hidden when online admission is off', 'fields' => array(
                'title' => $title('Admissions are open'),
                'text' => array('type' => 'textarea', 'label' => 'Text', 'default' => 'Apply online in a few minutes.'),
                'button' => array('type' => 'text', 'label' => 'Button text', 'default' => 'Apply now'),
            )),
            'courses' => array('label' => 'Online courses', 'icon' => 'fa-laptop', 'group' => 'dynamic', 'hint' => 'Needs the Online Course module', 'fields' => array('title' => $title('Online courses'), 'count' => $count(3), 'columns' => $columns)),
            // ------- the platform's own website
            'plans' => array('label' => 'Pricing table', 'icon' => 'fa-tags', 'group' => 'platform', 'hint' => 'From Subscription > Pricing Bands', 'fields' => array('title' => $title('Pricing'))),
            'platform_stats' => array('label' => 'Number of schools', 'icon' => 'fa-university', 'group' => 'platform', 'hint' => 'Live count of schools', 'fields' => array('label' => array('type' => 'text', 'label' => 'Label', 'default' => 'Schools on the platform'))),
            'register' => array('label' => 'Register-a-school button', 'icon' => 'fa-rocket', 'group' => 'platform', 'hint' => 'Links to the plans page', 'fields' => array(
                'title' => $title('Bring your school online'),
                'text' => array('type' => 'textarea', 'label' => 'Text', 'default' => ''),
                'button' => array('type' => 'text', 'label' => 'Button text', 'default' => 'Register your school'),
            )),
            // ------- content
            'text' => array('label' => 'Title with text', 'icon' => 'fa-font', 'group' => 'content', 'hint' => 'Use {school_name}, {phone}, {email}, {address}, {year}', 'fields' => array(
                'title' => $title(''),
                'body' => array('type' => 'html', 'label' => 'Text', 'default' => ''),
                'align' => array('type' => 'select', 'label' => 'Alignment', 'default' => 'left', 'options' => array('left' => 'Left', 'center' => 'Centre')),
            )),
            'image' => array('label' => 'Image', 'icon' => 'fa-image', 'group' => 'media', 'fields' => array(
                'src' => array('type' => 'image', 'label' => 'Image', 'default' => ''),
                'alt' => array('type' => 'text', 'label' => 'Description (for screen readers)', 'default' => ''),
                'decorative' => array('type' => 'check', 'label' => 'Decorative only: it needs no description', 'default' => 0),
                'link' => array('type' => 'url', 'label' => 'Link (optional)', 'default' => ''),
            )),
            'image_text' => array('label' => 'Image with text', 'icon' => 'fa-columns', 'group' => 'media', 'fields' => array(
                'src' => array('type' => 'image', 'label' => 'Image', 'default' => ''),
                'alt' => array('type' => 'text', 'label' => 'Image description (for screen readers)', 'default' => ''),
                'side' => array('type' => 'select', 'label' => 'Image side', 'default' => 'left', 'options' => array('left' => 'Left', 'right' => 'Right')),
                'title' => $title(''),
                'body' => array('type' => 'html', 'label' => 'Text', 'default' => ''),
            )),
            'video' => array('label' => 'Video', 'icon' => 'fa-youtube-play', 'group' => 'media', 'fields' => array('url' => array('type' => 'url', 'label' => 'YouTube or Vimeo link', 'default' => ''))),
            'button' => array('label' => 'Button', 'icon' => 'fa-hand-pointer-o', 'group' => 'design', 'fields' => array(
                'text' => array('type' => 'text', 'label' => 'Button text', 'default' => 'Learn more'),
                'link' => array('type' => 'url', 'label' => 'Link', 'default' => ''),
                'align' => array('type' => 'select', 'label' => 'Alignment', 'default' => 'center', 'options' => array('left' => 'Left', 'center' => 'Centre')),
            )),
            'testimonials' => array('label' => 'Testimonials', 'icon' => 'fa-quote-left', 'group' => 'content', 'fields' => array(
                'title' => $title('What parents say'),
                'items' => array('type' => 'items', 'label' => 'Testimonials', 'default' => array(), 'max' => 12, 'fields' => array(
                    'quote' => array('type' => 'textarea', 'label' => 'Quote', 'default' => ''),
                    'name' => array('type' => 'text', 'label' => 'Name', 'default' => ''),
                )),
            )),
            'faq' => array('label' => 'FAQ', 'icon' => 'fa-question-circle', 'group' => 'content', 'fields' => array(
                'title' => $title('Frequently asked questions'),
                'items' => array('type' => 'items', 'label' => 'Questions', 'default' => array(), 'max' => 30, 'fields' => array(
                    'q' => array('type' => 'text', 'label' => 'Question', 'default' => ''),
                    'a' => array('type' => 'textarea', 'label' => 'Answer', 'default' => ''),
                )),
            )),
            'contact_form' => array('label' => 'Contact form', 'icon' => 'fa-envelope-o', 'group' => 'content', 'hint' => 'One form per page; not on the homepage', 'fields' => array('title' => $title('Contact us'))),
            'complaint_form' => array('label' => 'Complaint form', 'icon' => 'fa-commenting-o', 'group' => 'content', 'hint' => 'One form per page; not on the homepage', 'fields' => array('title' => $title('Make a complaint'))),
            'map' => array('label' => 'Map', 'icon' => 'fa-map-marker', 'group' => 'media', 'fields' => array('address' => array('type' => 'text', 'label' => 'Address (empty: the school\'s address)', 'default' => ''))),
            'spacer' => array('label' => 'Spacer', 'icon' => 'fa-arrows-v', 'group' => 'design', 'fields' => array('height' => array('type' => 'number', 'label' => 'Height (pixels)', 'default' => 40, 'min' => 10, 'max' => 200))),
        );
    }

    /** The catalogue for this site: platform widgets only on the platform's own website. */
    public static function available($is_platform)
    {
        $out = array();
        foreach (self::catalogue() as $type => $w) {
            if ($w['group'] !== 'platform' || $is_platform) {
                $out[$type] = $w;
            }
        }
        return $out;
    }

    private static function widget($type, array $settings = array())
    {
        return array('type' => $type, 'settings' => $settings);
    }

    /** The ready-made homepage. */
    public static function starter_home($is_platform)
    {
        if ($is_platform) {
            return array('sections' => array(
                array('layout' => '1', 'bg' => 'light', 'full' => 1, 'cols' => array(array(
                    self::widget('text', array('title' => 'Run your whole school in one place', 'align' => 'center',
                        'body' => '<p>Admissions, fees, attendance, exams, staff, communication and your own school website, on your own web address.</p>')),
                    self::widget('register', array('title' => '', 'text' => '', 'button' => 'Register your school')),
                ))),
                array('layout' => '1', 'bg' => 'none', 'full' => 0, 'cols' => array(array(self::widget('platform_stats')))),
                array('layout' => '1', 'bg' => 'none', 'full' => 0, 'cols' => array(array(self::widget('plans')))),
                array('layout' => '1-1-1', 'bg' => 'light', 'full' => 1, 'cols' => array(
                    array(self::widget('text', array('title' => 'Fees and payments', 'body' => '<p>Parents pay online; every payment is recorded automatically.</p>'))),
                    array(self::widget('text', array('title' => 'Academics', 'body' => '<p>Classes, timetables, attendance, exams and report cards.</p>'))),
                    array(self::widget('text', array('title' => 'Your own website', 'body' => '<p>Build your school\'s site by dragging sections into place.</p>'))),
                )),
            ));
        }
        return array('sections' => array(
            array('layout' => '1', 'bg' => 'none', 'full' => 1, 'cols' => array(array(self::widget('banner')))),
            array('layout' => '1', 'bg' => 'none', 'full' => 0, 'cols' => array(array(
                self::widget('text', array('title' => 'Welcome to {school_name}', 'align' => 'center',
                    'body' => '<p>Tell visitors about your school here: your mission, your values and what makes you different.</p>')),
            ))),
            array('layout' => '1', 'bg' => 'light', 'full' => 1, 'cols' => array(array(self::widget('stats')))),
            array('layout' => '1-1', 'bg' => 'none', 'full' => 0, 'cols' => array(array(self::widget('news')), array(self::widget('events')))),
            array('layout' => '1', 'bg' => 'none', 'full' => 0, 'cols' => array(array(self::widget('gallery')))),
            array('layout' => '1', 'bg' => 'light', 'full' => 1, 'cols' => array(array(self::widget('admission')))),
        ));
    }

    // ------------------------------------------------------------------ cleaning

    /**
     * Any layout, old or new, as a block tree: {"v":2,"blocks":[...]}. Layouts from the first builder
     * (sections of columns of widgets) become sections holding columns holding blocks.
     */
    public static function normalize($layout)
    {
        if (is_string($layout)) {
            $layout = json_decode($layout, true);
        }
        if (!is_array($layout)) {
            return array('v' => 2, 'blocks' => array());
        }
        if (isset($layout['blocks']) && is_array($layout['blocks'])) {
            return array('v' => 2, 'blocks' => $layout['blocks']);
        }
        $blocks = array();
        foreach (isset($layout['sections']) && is_array($layout['sections']) ? $layout['sections'] : array() as $sec) {
            if (!is_array($sec)) {
                continue;
            }
            $leaf = function ($w) {
                return array('type' => isset($w['type']) ? (string) $w['type'] : '', 'attrs' => isset($w['settings']) && is_array($w['settings']) ? $w['settings'] : array());
            };
            $cols = isset($sec['cols']) && is_array($sec['cols']) ? $sec['cols'] : array();
            $layout_key = isset($sec['layout'], self::LAYOUTS[$sec['layout']]) ? (string) $sec['layout'] : '1';
            if ($layout_key === '1') {
                $inner = array_map($leaf, isset($cols[0]) && is_array($cols[0]) ? $cols[0] : array());
            } else {
                $columns = array();
                foreach (self::LAYOUTS[$layout_key] as $i => $width) {
                    $columns[] = array('type' => 'column', 'attrs' => array(), 'inner' => array_map($leaf, isset($cols[$i]) && is_array($cols[$i]) ? $cols[$i] : array()));
                }
                $inner = array(array('type' => 'columns', 'attrs' => array('layout' => $layout_key), 'inner' => $columns));
            }
            $blocks[] = array('type' => 'section', 'attrs' => array('bg' => isset($sec['bg']) ? $sec['bg'] : 'none', 'full' => empty($sec['full']) ? 0 : 1, 'padding' => 'm'), 'inner' => $inner);
        }
        return array('v' => 2, 'blocks' => $blocks);
    }

    /**
     * Cleans a layout from the editor: only known blocks, nested only where allowed, known fields,
     * values limited, HTML reduced to basic formatting. Whatever the browser sent, what is saved is
     * safe to show to visitors.
     *
     * @param string|array $input JSON or decoded layout (old or new form)
     * @return array the clean layout, always in the new form
     */
    public static function clean($input, $is_platform, $allow_forms = true)
    {
        if (is_string($input)) {
            if (strlen($input) > self::MAX_JSON) {
                throw new InvalidArgumentException('This page is too large to save.');
            }
            $input = json_decode($input, true);
        }
        if (!is_array($input) || (!isset($input['blocks']) && !isset($input['sections']))) {
            throw new InvalidArgumentException('The layout could not be read.');
        }
        $state = array('catalogue' => self::available($is_platform), 'form_used' => !$allow_forms, 'count' => 0);
        return array('v' => 2, 'blocks' => self::clean_blocks(self::normalize($input)['blocks'], 'root', $state));
    }

    /** The blocks a container may hold: sections hold columns or content, columns only content. */
    private static function clean_blocks($blocks, $context, array &$state)
    {
        $out = array();
        foreach (array_slice(is_array($blocks) ? $blocks : array(), 0, self::MAX_INNER) as $b) {
            if ($state['count'] >= self::MAX_BLOCKS) {
                break;
            }
            $type = is_array($b) && isset($b['type']) ? (string) $b['type'] : '';
            if (!isset($state['catalogue'][$type]) || $type === 'column') {
                continue;
            }
            if (($type === 'section' && $context !== 'root') || ($type === 'columns' && $context === 'column')) {
                continue; // no sections inside sections or columns, no columns inside columns
            }
            if (isset(self::FORMS[$type])) {
                if ($state['form_used']) {
                    continue;
                }
                $state['form_used'] = true;
            }
            $state['count']++;
            $attrs = isset($b['attrs']) && is_array($b['attrs']) ? $b['attrs'] : (isset($b['settings']) && is_array($b['settings']) ? $b['settings'] : array());
            $clean = array('type' => $type, 'attrs' => self::clean_fields($state['catalogue'][$type]['fields'], $attrs));
            if ($type === 'section') {
                $clean['inner'] = self::clean_blocks(isset($b['inner']) ? $b['inner'] : array(), 'section', $state);
            } elseif ($type === 'columns') {
                $widths = self::LAYOUTS[$clean['attrs']['layout']];
                $given = isset($b['inner']) && is_array($b['inner']) ? array_values($b['inner']) : array();
                $columns = array();
                foreach ($widths as $i => $width) {
                    $columns[] = isset($given[$i]['inner']) && is_array($given[$i]['inner']) ? $given[$i]['inner'] : array();
                }
                // Fewer columns than before: the extra columns' blocks join the last one.
                foreach (array_slice($given, count($widths)) as $extra) {
                    if (isset($extra['inner']) && is_array($extra['inner'])) {
                        $columns[count($widths) - 1] = array_merge($columns[count($widths) - 1], $extra['inner']);
                    }
                }
                $clean['inner'] = array();
                foreach ($columns as $inner) {
                    $clean['inner'][] = array('type' => 'column', 'attrs' => array(), 'inner' => self::clean_blocks($inner, 'column', $state));
                }
            }
            $out[] = $clean;
        }
        return $out;
    }

    /**
     * What stops a page being published: images without a description (unless marked decorative).
     * @return string[] one line per problem
     */
    public static function publish_problems($layout)
    {
        $problems = array();
        self::walk(self::normalize($layout)['blocks'], function ($b) use (&$problems) {
            $a = isset($b['attrs']) ? $b['attrs'] : array();
            if ($b['type'] === 'image' && !empty($a['src']) && trim((string) $a['alt']) === '' && empty($a['decorative'])) {
                $problems[] = 'An image has no description. Add one, or mark it as decorative.';
            } elseif ($b['type'] === 'image_text' && !empty($a['src']) && trim((string) $a['alt']) === '') {
                $problems[] = 'An "image with text" block has no image description.';
            } elseif ($b['type'] === 'image_gallery') {
                foreach (isset($a['images']) ? $a['images'] : array() as $img) {
                    if (trim((string) $img['alt']) === '') {
                        $problems[] = 'A gallery image has no description.';
                        break;
                    }
                }
            }
        });
        return array_values(array_unique($problems));
    }

    /** Calls $fn for every block in the tree, containers included. */
    private static function walk(array $blocks, callable $fn)
    {
        foreach ($blocks as $b) {
            if (!is_array($b) || !isset($b['type'])) {
                continue;
            }
            $fn($b);
            if (!empty($b['inner']) && is_array($b['inner'])) {
                self::walk($b['inner'], $fn);
            }
        }
    }

    private static function clean_fields(array $fields, array $given)
    {
        $out = array();
        foreach ($fields as $name => $f) {
            $v = array_key_exists($name, $given) ? $given[$name] : $f['default'];
            switch ($f['type']) {
                case 'number':
                    $out[$name] = max($f['min'], min($f['max'], (int) (is_scalar($v) ? $v : $f['default'])));
                    break;
                case 'check':
                    $out[$name] = empty($v) ? 0 : 1;
                    break;
                case 'select':
                    $out[$name] = is_scalar($v) && isset($f['options'][(string) $v]) ? (string) $v : $f['default'];
                    break;
                case 'url':
                case 'image':
                    $out[$name] = self::clean_url(is_scalar($v) ? (string) $v : '');
                    break;
                case 'html':
                    $out[$name] = self::clean_html(is_scalar($v) ? (string) $v : '');
                    break;
                case 'inline':
                    $out[$name] = self::clean_html(is_scalar($v) ? (string) $v : '', self::INLINE_TAGS);
                    break;
                case 'listhtml':
                    $out[$name] = self::clean_list(is_scalar($v) ? (string) $v : '');
                    break;
                case 'table':
                    $rows = array();
                    foreach (array_slice(is_array($v) ? array_values($v) : array(), 0, 40) as $row) {
                        $cells = array();
                        foreach (array_slice(is_array($row) ? array_values($row) : array(), 0, 8) as $cell) {
                            $cells[] = mb_substr(trim(strip_tags(is_scalar($cell) ? (string) $cell : '')), 0, 500);
                        }
                        $rows[] = $cells;
                    }
                    // Every row as wide as the widest.
                    $width = max(1, $rows ? max(array_map('count', $rows)) : 1);
                    foreach ($rows as $i => $cells) {
                        $rows[$i] = array_pad($cells, $width, '');
                    }
                    $out[$name] = $rows ?: $f['default'];
                    break;
                case 'images':
                    $images = array();
                    foreach (array_slice(is_array($v) ? $v : array(), 0, $f['max']) as $img) {
                        $src = is_array($img) && isset($img['src']) && is_scalar($img['src']) ? self::clean_url((string) $img['src']) : '';
                        if ($src !== '') {
                            $images[] = array('src' => $src, 'alt' => mb_substr(trim(strip_tags(isset($img['alt']) && is_scalar($img['alt']) ? (string) $img['alt'] : '')), 0, 300));
                        }
                    }
                    $out[$name] = $images;
                    break;
                case 'items':
                    $items = array();
                    foreach (array_slice(is_array($v) ? $v : array(), 0, $f['max']) as $item) {
                        if (is_array($item)) {
                            $items[] = self::clean_fields($f['fields'], $item);
                        }
                    }
                    $out[$name] = $items;
                    break;
                default: // text, textarea: plain text, escaped when shown
                    $text = trim(strip_tags(is_scalar($v) ? (string) $v : ''));
                    $out[$name] = mb_substr(str_replace('[form-builder:', '', $text), 0, $f['type'] === 'textarea' ? 5000 : 300);
            }
        }
        return $out;
    }

    /** http(s), mailto, tel or a path within the site; anything else (javascript:, data:...) is dropped. */
    public static function clean_url($url)
    {
        $url = trim(preg_replace('/[\x00-\x20]+/', ' ', $url));
        if ($url === '' || strlen($url) > 1000) {
            return '';
        }
        // This site's own address is stored as a path, so it survives a move to HTTPS or a custom domain.
        if (function_exists('base_url')) {
            $own = preg_replace('~^https?://~i', '', base_url());
            $bare = preg_replace('~^https?://~i', '', $url);
            if (stripos($bare, $own) === 0 && $own !== '') {
                $url = substr($bare, strlen($own));
                if ($url === '') {
                    return '';
                }
            }
        }
        if (preg_match('~^(https?://|mailto:|tel:)~i', $url)) {
            return $url;
        }
        if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $url) || strpos($url, '//') === 0 || strpos($url, '\\') !== false) {
            return '';
        }
        return ltrim($url, '/');
    }

    /** Keeps basic formatting only: no scripts, styles, event handlers or embedded content. */
    public static function clean_html($html, array $allowed = null)
    {
        $html = str_replace('[form-builder:', '', mb_substr($html, 0, 20000));
        if (trim($html) === '') {
            return '';
        }
        $allowed = $allowed ?: array('p', 'br', 'b', 'strong', 'i', 'em', 'u', 'a', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote', 'span');
        $drop = array('script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'link', 'meta', 'base', 'noscript', 'template');
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="pwroot">' . $html . '</div>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $doc->getElementById('pwroot');
        if (!$root) {
            return '';
        }
        self::clean_node($root, $doc, $allowed, $drop);
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return trim($out);
    }

    /** List items only (each with inline formatting); loose lines become items. */
    public static function clean_list($html)
    {
        if (stripos($html, '<li') === false) {
            $lines = array_filter(array_map('trim', preg_split('/<br\s*\/?>|\n/i', $html)), 'strlen');
            $html = $lines ? '<li>' . implode('</li><li>', $lines) . '</li>' : '';
        }
        $clean = self::clean_html($html, array_merge(self::INLINE_TAGS, array('li')));
        // Keep only the items: text between them is dropped.
        preg_match_all('~<li>.*?</li>~s', $clean, $m);
        return implode('', $m[0]);
    }

    private static function clean_node(DOMNode $node, DOMDocument $doc, array $allowed, array $drop)
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $tag = strtolower($child->nodeName);
            if (in_array($tag, $drop, true)) {
                $node->removeChild($child);
                continue;
            }
            self::clean_node($child, $doc, $allowed, $drop);
            if (!in_array($tag, $allowed, true)) { // unknown tag: keep its contents
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            $href = $tag === 'a' ? self::clean_url($child->getAttribute('href')) : '';
            foreach (iterator_to_array($child->attributes) as $attr) {
                $child->removeAttribute($attr->nodeName);
            }
            if ($href !== '') {
                $child->setAttribute('href', $href);
                $child->setAttribute('rel', 'noopener nofollow');
            }
        }
    }

    // ------------------------------------------------------------------ data

    /** Replaces {school_name} etc. in text. Values come from the school's settings. */
    public function tokens()
    {
        $s = $this->CI->setting_model->get();
        $s = $s ? $s[0] : array();
        $get = function ($k) use ($s) { return isset($s[$k]) ? (string) $s[$k] : ''; };
        return array('{school_name}' => $get('name'), '{phone}' => $get('phone'), '{email}' => $get('email'), '{address}' => $get('address'), '{year}' => date('Y'));
    }

    /** Fetches what a widget shows; always the current school's rows (the tenant filter applies). */
    private function data($type, array $s)
    {
        $db = $this->CI->db;
        $today = date('Y-m-d');
        switch ($type) {
            case 'banner':
                $this->CI->load->model('cms_program_model');
                $banner = $this->CI->cms_program_model->getByCategory($this->CI->config->item('ci_front_banner_content') ?: 'banner');
                return $banner ? $this->CI->cms_program_model->front_cms_program_photos($banner[0]['id']) : array();
            case 'news':
                return $db->where('type', 'notice')->order_by('date', 'desc')->order_by('id', 'desc')->limit((int) $s['count'])->get('front_cms_programs')->result_array();
            case 'events':
                return $db->where('type', 'events')->where('event_end >=', $today)->order_by('event_start', 'asc')->limit((int) $s['count'])->get('front_cms_programs')->result_array();
            case 'gallery':
                return $db->where('type', 'gallery')->order_by('id', 'desc')->limit((int) $s['count'])->get('front_cms_programs')->result_array();
            case 'stats':
                $out = array();
                if (!empty($s['students'])) {
                    $out[] = array('value' => $db->where('is_active', 'yes')->count_all_results('students'), 'label' => 'Students');
                }
                if (!empty($s['staff'])) {
                    $out[] = array('value' => $db->where('is_active', 1)->count_all_results('staff'), 'label' => 'Staff');
                }
                if (!empty($s['classes'])) {
                    $out[] = array('value' => $db->count_all_results('classes'), 'label' => 'Classes');
                }
                foreach ($s['extra'] as $e) {
                    if ($e['value'] !== '' || $e['label'] !== '') {
                        $out[] = $e;
                    }
                }
                return $out;
            case 'staff':
                return $db->select('staff.name, staff.surname, staff.image, staff_designation.designation')->from('staff')
                    ->join('staff_designation', 'staff_designation.id = staff.designation', 'left')
                    ->where('staff.is_active', 1)->where('staff.show_on_website', 1)
                    ->order_by('staff.name', 'asc')->limit((int) $s['count'])->get()->result_array();
            case 'classes':
                return $db->select('class')->order_by('id', 'asc')->limit(100)->get('classes')->result_array();
            case 'admission':
                $setting = $this->CI->setting_model->getSetting();
                return !empty($setting->online_admission) ? array('open' => 1) : array();
            case 'courses':
                if (!$this->CI->module_lib->hasActive('online_course')) {
                    return array();
                }
                return $db->select('title, slug, course_thumbnail')->where('status', 1)->where('front_side_visibility', 'yes')
                    ->order_by('id', 'desc')->limit((int) $s['count'])->get('online_courses')->result_array();
            case 'plans':
                $this->CI->load->library('Plan_pricing');
                return $this->CI->plan_pricing->bands();
            case 'platform_stats':
                return array('schools' => $db->where(array('is_platform' => 0, 'cron_flag' => 1, 'status' => 1))->count_all_results('school'));
        }
        return array();
    }

    // ------------------------------------------------------------------ rendering

    /**
     * @param string|array $layout JSON or decoded, as saved (old or new form)
     * @param bool $preview true: empty dynamic blocks show why (only the admin sees a preview)
     * @return string HTML
     */
    public function render($layout, $preview = false)
    {
        $blocks = self::normalize($layout)['blocks'];
        if (!$blocks) {
            return '';
        }
        return $this->CI->load->view('front_widgets/styles', array(), true) . '<div class="pw-page">' . $this->render_blocks($blocks, $preview) . '</div>';
    }

    /** Blocks to HTML, without the page wrapper and styles (the editor shows blocks one by one). */
    public function render_blocks(array $blocks, $preview = false)
    {
        $this->CI->load->library('customlib');
        $ctx = array(
            'tokens' => $this->tokens(),
            'catalogue' => self::catalogue(),
            'platform' => class_exists('Tenant', false) && Tenant::is_platform(),
            'preview' => $preview,
        );
        return $this->render_list($blocks, $ctx);
    }

    private function render_list(array $blocks, array $ctx)
    {
        static $uid = 0;
        $html = '';
        foreach ($blocks as $b) {
            $type = is_array($b) && isset($b['type']) ? (string) $b['type'] : '';
            if (!isset($ctx['catalogue'][$type]) || ($ctx['catalogue'][$type]['group'] === 'platform' && !$ctx['platform'])) {
                continue;
            }
            $attrs = self::clean_fields($ctx['catalogue'][$type]['fields'], isset($b['attrs']) && is_array($b['attrs']) ? $b['attrs'] : array());
            $inner = isset($b['inner']) && is_array($b['inner']) ? $b['inner'] : array();
            if ($type === 'section') {
                $classes = 'pw-section pw-bg-' . html_escape($attrs['bg']) . ' pw-pad-' . html_escape($attrs['padding']) . (!empty($attrs['full']) ? ' pw-full' : '');
                $html .= '<section class="' . $classes . '"><div class="pw-inner">' . $this->render_list($inner, $ctx) . '</div></section>';
                continue;
            }
            if ($type === 'columns') {
                $body = '';
                foreach (self::LAYOUTS[$attrs['layout']] as $i => $width) {
                    $col = isset($inner[$i]['inner']) && is_array($inner[$i]['inner']) ? $inner[$i]['inner'] : array();
                    $body .= '<div class="col-md-' . (int) $width . ' col-sm-12">' . $this->render_list($col, $ctx) . '</div>';
                }
                $html .= '<div class="row pw-columns">' . $body . '</div>';
                continue;
            }
            try {
                $html .= $this->CI->load->view('front_widgets/widget', array(
                    'type' => $type, 's' => $attrs, 'd' => $this->data($type, $attrs), 'tokens' => $ctx['tokens'],
                    'preview' => $ctx['preview'], 'label' => $ctx['catalogue'][$type]['label'],
                    'hint' => isset($ctx['catalogue'][$type]['hint']) ? $ctx['catalogue'][$type]['hint'] : '',
                    'uid' => 'pw' . (++$uid),
                ), true);
            } catch (Throwable $e) {
                // One broken block must not take the page down.
                log_message('error', 'Page block ' . $type . ': ' . $e->getMessage());
            }
        }
        return $html;
    }

    /** The form name a layout uses (Welcome::page() builds and handles it), or null. */
    public static function layout_form($layout)
    {
        $form = null;
        self::walk(self::normalize($layout)['blocks'], function ($b) use (&$form) {
            if ($form === null && isset(self::FORMS[$b['type']])) {
                $form = self::FORMS[$b['type']];
            }
        });
        return $form;
    }

    // ------------------------------------------------------------------ patterns and templates

    /**
     * Ready-made patterns: groups of blocks to insert, and whole-page templates. Text is a starting
     * point the school replaces; nothing here claims facts about the school.
     * @return array key => title, description, template (bool), blocks
     */
    public static function patterns($is_platform)
    {
        $p = function ($html, $align = 'left') { return array('type' => 'paragraph', 'attrs' => array('html' => $html, 'align' => $align)); };
        $h = function ($html, $level = '2', $align = 'left') { return array('type' => 'heading', 'attrs' => array('html' => $html, 'level' => $level, 'align' => $align)); };
        $section = function (array $inner, $bg = 'none', $full = 0) { return array('type' => 'section', 'attrs' => array('bg' => $bg, 'padding' => 'm', 'full' => $full), 'inner' => $inner); };
        $columns = function ($layout, array $cols) {
            return array('type' => 'columns', 'attrs' => array('layout' => $layout), 'inner' => array_map(function ($c) { return array('type' => 'column', 'attrs' => array(), 'inner' => $c); }, $cols));
        };
        $button = function ($text, $link, $align = 'left') { return array('type' => 'button', 'attrs' => array('text' => $text, 'link' => $link, 'align' => $align)); };
        $feature = function ($title) use ($h, $p) { return array($h($title, '3'), $p('Write a sentence or two about this.')); };

        $out = array(
            'hero' => array('title' => 'Welcome banner', 'description' => 'A large heading, a sentence and a button', 'template' => false, 'blocks' => array(
                $section(array($h('Welcome to {school_name}', '2', 'center'), $p('Say in one sentence what makes your school special.', 'center'), $button('Apply now', 'online_admission', 'center')), 'light', 1),
            )),
            'features' => array('title' => 'Three points', 'description' => 'Three columns, each with a heading and text', 'template' => false, 'blocks' => array(
                $section(array($columns('1-1-1', array($feature('Our teaching'), $feature('Our facilities'), $feature('Our community'))))),
            )),
            'admissions' => array('title' => 'Admissions call to action', 'description' => 'Invites parents to apply online', 'template' => false, 'blocks' => array(
                $section(array(array('type' => 'admission', 'attrs' => array())), 'light', 1),
            )),
            'contact_strip' => array('title' => 'Contact strip', 'description' => 'Phone, email and address side by side', 'template' => false, 'blocks' => array(
                $section(array($columns('1-1-1', array(
                    array($h('Call us', '3'), $p('{phone}')),
                    array($h('Email us', '3'), $p('{email}')),
                    array($h('Visit us', '3'), $p('{address}')),
                ))), 'light', 1),
            )),
            'faq' => array('title' => 'Questions and answers', 'description' => 'A list of common questions', 'template' => false, 'blocks' => array(
                $section(array(array('type' => 'faq', 'attrs' => array('title' => 'Frequently asked questions', 'items' => array(
                    array('q' => 'When does the school day start and end?', 'a' => 'Write the answer here.'),
                    array('q' => 'How do I pay school fees?', 'a' => 'Write the answer here.'),
                ))))),
            )),
            'image_left' => array('title' => 'Picture and text', 'description' => 'An image beside a heading and text', 'template' => false, 'blocks' => array(
                $section(array($columns('1-1', array(
                    array(array('type' => 'image', 'attrs' => array())),
                    array($h('A heading'), $p('Tell your story here.')),
                ))))),
            ),
            // Whole pages
            'page_home' => array('title' => 'Homepage', 'description' => 'Banner, welcome, numbers, news, events, gallery and admissions', 'template' => true,
                'blocks' => self::normalize(self::starter_home($is_platform))['blocks']),
            'page_about' => array('title' => 'About page', 'description' => 'Your story, values and a picture', 'template' => true, 'blocks' => array(
                $section(array($h('About {school_name}'), $p('Tell visitors how the school started, who it serves and what it believes in.')), 'none'),
                $section(array($columns('1-1', array(
                    array(array('type' => 'image', 'attrs' => array())),
                    array($h('Our values', '3'), array('type' => 'list', 'attrs' => array('html' => '<li>A value</li><li>Another value</li><li>A third value</li>', 'ordered' => 0))),
                ))), 'light', 1),
            )),
            'page_admissions' => array('title' => 'Admissions page', 'description' => 'How to apply, fees questions and the application button', 'template' => true, 'blocks' => array(
                $section(array($h('Admissions'), $p('Explain who can apply, when, and what parents need to bring.'))),
                $section(array($columns('1-1-1', array($feature('1. Apply online'), $feature('2. Assessment'), $feature('3. Admission'))))),
                $section(array(array('type' => 'admission', 'attrs' => array())), 'light', 1),
            )),
            'page_contact' => array('title' => 'Contact page', 'description' => 'Contact details, a message form and a map', 'template' => true, 'blocks' => array(
                $section(array($h('Contact us'), $p('{phone} &middot; {email}'), $p('{address}'))),
                $section(array($columns('1-1', array(
                    array(array('type' => 'contact_form', 'attrs' => array('title' => 'Send us a message'))),
                    array(array('type' => 'map', 'attrs' => array())),
                )))),
            )),
        );
        return $out;
    }
}
