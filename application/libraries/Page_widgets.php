<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Page builder widgets: the catalogue, cleaning a layout before it is saved, fetching each widget's
 * data (always the current school's) and rendering a layout to HTML.
 *
 * A layout is JSON:
 *   {"sections":[{"layout":"1-1","bg":"light","full":0,"cols":[[widget,...],[widget,...]]}, ...]}
 *   widget: {"type":"news","settings":{...}}
 *
 * The rendered HTML goes into the page's `description`, so every theme shows it unchanged, and the
 * contact and complaint widgets use the existing [form-builder:...] handling of Welcome::page().
 *
 * Public pages: students appear only as totals; staff only if marked "show on website".
 */
class Page_widgets
{
    const LAYOUTS = array('1' => array(12), '1-1' => array(6, 6), '1-2' => array(4, 8), '2-1' => array(8, 4), '1-1-1' => array(4, 4, 4));
    const BACKGROUNDS = array('none' => 'None', 'light' => 'Light', 'dark' => 'Dark');
    const MAX_SECTIONS = 40;
    const MAX_WIDGETS = 12; // per column
    const MAX_JSON = 400000;
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
            'text' => array('label' => 'Heading and text', 'icon' => 'fa-font', 'group' => 'content', 'hint' => 'Use {school_name}, {phone}, {email}, {address}, {year}', 'fields' => array(
                'title' => $title(''),
                'body' => array('type' => 'html', 'label' => 'Text', 'default' => ''),
                'align' => array('type' => 'select', 'label' => 'Alignment', 'default' => 'left', 'options' => array('left' => 'Left', 'center' => 'Centre')),
            )),
            'image' => array('label' => 'Image', 'icon' => 'fa-image', 'group' => 'content', 'fields' => array(
                'src' => array('type' => 'image', 'label' => 'Image', 'default' => ''),
                'alt' => array('type' => 'text', 'label' => 'Description (for screen readers)', 'default' => ''),
                'link' => array('type' => 'url', 'label' => 'Link (optional)', 'default' => ''),
            )),
            'image_text' => array('label' => 'Image with text', 'icon' => 'fa-columns', 'group' => 'content', 'fields' => array(
                'src' => array('type' => 'image', 'label' => 'Image', 'default' => ''),
                'side' => array('type' => 'select', 'label' => 'Image side', 'default' => 'left', 'options' => array('left' => 'Left', 'right' => 'Right')),
                'title' => $title(''),
                'body' => array('type' => 'html', 'label' => 'Text', 'default' => ''),
            )),
            'video' => array('label' => 'Video', 'icon' => 'fa-youtube-play', 'group' => 'content', 'fields' => array('url' => array('type' => 'url', 'label' => 'YouTube or Vimeo link', 'default' => ''))),
            'button' => array('label' => 'Button', 'icon' => 'fa-hand-pointer-o', 'group' => 'content', 'fields' => array(
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
            'map' => array('label' => 'Map', 'icon' => 'fa-map-marker', 'group' => 'content', 'fields' => array('address' => array('type' => 'text', 'label' => 'Address (empty: the school\'s address)', 'default' => ''))),
            'spacer' => array('label' => 'Spacer', 'icon' => 'fa-arrows-v', 'group' => 'content', 'fields' => array('height' => array('type' => 'number', 'label' => 'Height (pixels)', 'default' => 40, 'min' => 10, 'max' => 200))),
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
     * Cleans a layout from the builder: only known widgets and fields, values limited, HTML reduced to
     * basic formatting. Whatever the browser sent, what is saved is safe to show to visitors.
     *
     * @param string|array $input JSON or decoded layout
     * @return array the clean layout
     */
    public static function clean($input, $is_platform, $allow_forms = true)
    {
        if (is_string($input)) {
            if (strlen($input) > self::MAX_JSON) {
                throw new InvalidArgumentException('This page is too large to save.');
            }
            $input = json_decode($input, true);
        }
        if (!is_array($input) || !isset($input['sections']) || !is_array($input['sections'])) {
            throw new InvalidArgumentException('The layout could not be read.');
        }
        $catalogue = self::available($is_platform);
        $form_used = !$allow_forms;
        $sections = array();
        foreach (array_slice($input['sections'], 0, self::MAX_SECTIONS) as $sec) {
            if (!is_array($sec)) {
                continue;
            }
            $layout = isset($sec['layout'], self::LAYOUTS[$sec['layout']]) ? (string) $sec['layout'] : '1';
            $bg = isset($sec['bg'], self::BACKGROUNDS[$sec['bg']]) ? $sec['bg'] : 'none';
            $cols = array();
            foreach (self::LAYOUTS[$layout] as $i => $width) {
                $widgets = array();
                $given = isset($sec['cols'][$i]) && is_array($sec['cols'][$i]) ? $sec['cols'][$i] : array();
                foreach (array_slice($given, 0, self::MAX_WIDGETS) as $w) {
                    $type = is_array($w) && isset($w['type']) ? (string) $w['type'] : '';
                    if (!isset($catalogue[$type])) {
                        continue;
                    }
                    if (isset(self::FORMS[$type])) {
                        if ($form_used) {
                            continue;
                        }
                        $form_used = true;
                    }
                    $settings = isset($w['settings']) && is_array($w['settings']) ? $w['settings'] : array();
                    $widgets[] = array('type' => $type, 'settings' => self::clean_fields($catalogue[$type]['fields'], $settings));
                }
                $cols[] = $widgets;
            }
            $sections[] = array('layout' => $layout, 'bg' => $bg, 'full' => empty($sec['full']) ? 0 : 1, 'cols' => $cols);
        }
        return array('sections' => $sections);
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
    public static function clean_html($html)
    {
        $html = str_replace('[form-builder:', '', mb_substr($html, 0, 20000));
        if (trim($html) === '') {
            return '';
        }
        $allowed = array('p', 'br', 'b', 'strong', 'i', 'em', 'u', 'a', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote', 'span');
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
     * @param string|array $layout JSON or decoded, as saved
     * @param bool $preview true: empty dynamic widgets show why (only the admin sees a preview)
     * @return string HTML
     */
    public function render($layout, $preview = false)
    {
        $layout = is_string($layout) ? json_decode($layout, true) : $layout;
        if (!is_array($layout) || empty($layout['sections'])) {
            return '';
        }
        $this->CI->load->library('customlib');
        $tokens = $this->tokens();
        $catalogue = self::catalogue();
        $is_platform = class_exists('Tenant', false) && Tenant::is_platform();
        $html = $this->CI->load->view('front_widgets/styles', array(), true) . '<div class="pw-page">';
        foreach ($layout['sections'] as $n => $section) {
            $widths = isset(self::LAYOUTS[$section['layout']]) ? self::LAYOUTS[$section['layout']] : array(12);
            $body = '';
            foreach ($widths as $i => $width) {
                $col = '';
                foreach (isset($section['cols'][$i]) ? $section['cols'][$i] : array() as $k => $w) {
                    $type = $w['type'];
                    if (!isset($catalogue[$type]) || ($catalogue[$type]['group'] === 'platform' && !$is_platform)) {
                        continue;
                    }
                    $settings = self::clean_fields($catalogue[$type]['fields'], isset($w['settings']) ? $w['settings'] : array());
                    try {
                        $col .= $this->CI->load->view('front_widgets/widget', array(
                            'type' => $type, 's' => $settings, 'd' => $this->data($type, $settings), 'tokens' => $tokens,
                            'preview' => $preview, 'label' => $catalogue[$type]['label'], 'hint' => isset($catalogue[$type]['hint']) ? $catalogue[$type]['hint'] : '',
                            'uid' => 'pw' . $n . '_' . $i . '_' . $k,
                        ), true);
                    } catch (Throwable $e) {
                        // One broken widget must not take the page down.
                        log_message('error', 'Page widget ' . $type . ': ' . $e->getMessage());
                    }
                }
                $body .= '<div class="col-md-' . (int) $width . ' col-sm-12">' . $col . '</div>';
            }
            $classes = 'pw-section pw-bg-' . html_escape($section['bg']) . (!empty($section['full']) ? ' pw-full' : '');
            $html .= '<section class="' . $classes . '"><div class="pw-inner"><div class="row">' . $body . '</div></div></section>';
        }
        return $html . '</div>';
    }

    /** The form name a layout uses (Welcome::page() builds and handles it), or null. */
    public static function layout_form($layout)
    {
        $layout = is_string($layout) ? json_decode($layout, true) : $layout;
        foreach (isset($layout['sections']) ? $layout['sections'] : array() as $section) {
            foreach ($section['cols'] as $col) {
                foreach ($col as $w) {
                    if (isset(self::FORMS[$w['type']])) {
                        return self::FORMS[$w['type']];
                    }
                }
            }
        }
        return null;
    }
}
