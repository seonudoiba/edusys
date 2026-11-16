<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform website's design (views/themes/platform): a preset from config/platform_theme.php
 * with the platform's own changes, and the homepage slider's slides. Changed on the platform's
 * Front CMS > Website design (controllers/admin/front/Design.php).
 *
 * Saved in front_cms_settings.theme_options as
 * {"theme":"platform","preset":"edusys","options":{...},"slides":[...]} (theme stays "platform").
 * Colours are turned into the CSS variables of backend/themes/platform/site.css; each one is
 * adjusted so text on it stays readable whatever colour is picked.
 */
class Platform_theme
{
    const THEME = 'platform';
    const IMAGE_DIR = 'uploads/gallery/media/';

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('platform_theme');
        $this->CI->load->library('Site_theme');
    }

    public function presets()
    {
        return $this->CI->config->item('platform_themes');
    }

    public function fields()
    {
        return $this->CI->config->item('platform_theme_options');
    }

    public function fonts()
    {
        return $this->CI->site_theme->fonts();
    }

    public function visuals()
    {
        return $this->CI->config->item('platform_slide_visuals');
    }

    public function max_slides()
    {
        return (int) $this->CI->config->item('platform_max_slides');
    }

    public function default_slides()
    {
        return $this->CI->config->item('platform_default_slides');
    }

    /**
     * The design in effect: ['preset' => key, 'options' => [...], 'slides' => [...]].
     * @param object|null $front_setting front_cms_settings row
     * @param array|null $override unsaved choices to try (the design page's preview)
     */
    public function design($front_setting, ?array $override = null)
    {
        $saved = isset($front_setting->theme_options) ? json_decode((string) $front_setting->theme_options, true) : null;
        if (!is_array($saved) || !isset($saved['theme']) || $saved['theme'] !== self::THEME) {
            $saved = array();
        }
        $source = $override !== null ? $override : $saved;
        $presets = $this->presets();
        $preset = isset($source['preset']) && isset($presets[$source['preset']]) ? $source['preset'] : 'edusys';
        $options = array_merge($presets[$preset]['options'], $this->clean(isset($source['options']) ? $source['options'] : array()));
        $slides = isset($source['slides']) && is_array($source['slides']) ? $this->clean_slides($source['slides']) : $this->default_slides();
        return array('preset' => $preset, 'options' => $options, 'slides' => $slides);
    }

    /** What is stored in front_cms_settings.theme_options. */
    public function to_json($preset, array $options, array $slides)
    {
        $presets = $this->presets();
        return json_encode(array(
            'theme' => self::THEME,
            'preset' => isset($presets[$preset]) ? $preset : 'edusys',
            'options' => $this->clean($options),
            'slides' => $this->clean_slides($slides),
        ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Only known options with allowed values. */
    public function clean($input)
    {
        $out = array();
        if (!is_array($input)) {
            return $out;
        }
        $fonts = $this->fonts();
        foreach ($this->fields() as $name => $f) {
            if (!array_key_exists($name, $input) || !is_scalar($input[$name])) {
                continue;
            }
            $v = (string) $input[$name];
            if ($f['type'] === 'color' && preg_match('/^#[0-9a-fA-F]{6}$/', $v)) {
                $out[$name] = strtolower($v);
            } elseif ($f['type'] === 'font' && isset($fonts[$v])) {
                $out[$name] = $v;
            } elseif ($f['type'] === 'select' && isset($f['choices'][$v])) {
                $out[$name] = $v;
            }
        }
        return $out;
    }

    /** Up to max_slides() slides, plain text only; images must be files in IMAGE_DIR. */
    public function clean_slides($input)
    {
        $out = array();
        if (!is_array($input)) {
            return $out;
        }
        $fields = $this->CI->config->item('platform_slide_fields');
        $visuals = $this->visuals();
        foreach (array_values($input) as $slide) {
            if (!is_array($slide) || count($out) >= $this->max_slides()) {
                continue;
            }
            $s = array();
            foreach ($fields as $name => $f) {
                $v = isset($slide[$name]) && is_scalar($slide[$name]) ? (string) $slide[$name] : '';
                $v = $name === 'points' ? trim(preg_replace("/[ \t]+/", ' ', str_replace("\r", '', strip_tags($v)))) : trim(preg_replace('/\s+/', ' ', strip_tags($v)));
                $s[$name] = mb_substr($v, 0, $f['max']);
            }
            foreach (array('btn1_url', 'btn2_url') as $u) {
                if ($s[$u] !== '' && !$this->safe_url($s[$u])) {
                    $s[$u] = '';
                }
            }
            $image = isset($slide['image']) && is_string($slide['image']) ? $slide['image'] : '';
            $s['image'] = preg_match('~^' . preg_quote(self::IMAGE_DIR, '~') . 'slide_[a-f0-9]{16}\.(jpg|png|webp)$~', $image) ? $image : '';
            $s['visual'] = isset($slide['visual']) && isset($visuals[$slide['visual']]) ? $slide['visual'] : 'none';
            if (in_array($s['visual'], array('photo', 'background'), true) && $s['image'] === '') {
                $s['visual'] = 'none';
            }
            $s['show_price'] = !empty($slide['show_price']) ? 1 : 0;
            if ($s['title'] === '' && $s['image'] === '') {
                continue; // an empty slide
            }
            $out[] = $s;
        }
        return $out;
    }

    /** A page of this site ("plans", "/page/about-us"), a full http(s) address, or mailto:/tel:. */
    private function safe_url($url)
    {
        if (preg_match('~^(https?://|mailto:|tel:)~i', $url)) {
            return $url !== filter_var($url, FILTER_SANITIZE_URL) ? false : true;
        }
        return (bool) preg_match('~^/?[A-Za-z0-9][A-Za-z0-9_\-/.?=&#%]*$~', $url);
    }

    /** The link for a slide button. */
    public function link($url)
    {
        return preg_match('~^(https?://|mailto:|tel:)~i', $url) ? $url : site_url(ltrim($url, '/'));
    }

    // ------------------------------------------------------------------ output

    /** CSS variables for backend/themes/platform/site.css (its defaults are the EduSYS preset). */
    public function css(array $o)
    {
        $t = $this->CI->site_theme;
        $presets = $this->presets();
        $d = $presets['edusys']['options'];
        $fonts = $this->fonts();
        $vars = array();
        if ($o['primary'] !== $d['primary']) {
            $p = $this->darken_until($o['primary'], '#ffffff', 7);
            $vars += array(
                '--navy' => $p,
                '--navy-dark' => $t->mix($p, '#000000', 0.3),
                '--navy-deep' => $t->mix($p, '#000000', 0.5),
                '--ink' => $t->mix($p, '#000000', 0.55),
                '--tint' => $t->mix($p, '#ffffff', 0.96),
                '--tint-2' => $t->mix($p, '#ffffff', 0.91),
                '--rule' => $t->mix($p, '#ffffff', 0.9),
                '--rule-strong' => $t->mix($p, '#ffffff', 0.82),
            );
        }
        if ($o['accent'] !== $d['accent']) {
            $a = $this->darken_until($o['accent'], '#ffffff', 4.5);
            $vars += array(
                '--teal' => $a,
                '--teal-dark' => $t->mix($a, '#000000', 0.25),
                '--teal-light' => $t->mix($a, '#ffffff', 0.88),
                '--green' => $a,
            );
        }
        if ($o['highlight'] !== $d['highlight'] || $o['primary'] !== $d['primary']) {
            $deep = isset($vars['--navy-deep']) ? $vars['--navy-deep'] : '#031634';
            $g = $o['highlight'];
            for ($i = 0; $i < 12 && $t->contrast($g, $deep) < 4.5; $i++) {
                $g = $t->mix($g, '#ffffff', 0.15);
            }
            $light = $t->mix($g, '#ffffff', 0.85);
            $vars += array('--gold' => $g, '--gold-light' => $light, '--gold-ink' => $this->darken_until($g, $light, 4.5));
        }
        if ($o['heading_font'] !== $d['heading_font']) {
            $vars['--head'] = $fonts[$o['heading_font']][2];
        }
        if ($o['body_font'] !== $d['body_font']) {
            $vars['--sans'] = $fonts[$o['body_font']][2];
        }
        $vars['--text-base'] = array('s' => '15.5px', 'm' => '16.5px', 'l' => '17.5px')[$o['text_size']];
        $vars['--radius'] = array('square' => '4px', 'soft' => '12px', 'round' => '18px')[$o['corners']];
        $vars['--radius-sm'] = array('square' => '3px', 'soft' => '8px', 'round' => '12px')[$o['corners']];
        $vars['--radius-lg'] = array('square' => '4px', 'soft' => '16px', 'round' => '24px')[$o['corners']];
        $vars['--btn-radius'] = array('square' => '4px', 'soft' => '10px', 'pill' => '999px')[$o['buttons']];
        $vars['--width'] = $o['width'] === 'wide' ? '1320px' : '1180px';
        $vars['--overlay'] = array('light' => '55%', 'medium' => '78%', 'strong' => '92%')[$o['slider_overlay']];
        $css = ':root{';
        foreach ($vars as $k => $v) {
            $css .= $k . ':' . $v . ';';
        }
        return $css . '}';
    }

    /** The dark main colour actually used (for <meta name="theme-color">). */
    public function theme_color(array $o)
    {
        return $this->darken_until($o['primary'], '#ffffff', 7);
    }

    public function fonts_url(array $o)
    {
        $fonts = $this->fonts();
        $families = array_unique(array($fonts[$o['heading_font']][1], $fonts[$o['body_font']][1]));
        return 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $families) . '&display=swap';
    }

    public function body_classes(array $o)
    {
        return 'pt-hdr-' . $o['header_style'] . ' pt-ftr-' . $o['footer'] . ' pt-hero-' . $o['slider_height'];
    }

    private function darken_until($hex, $against, $ratio)
    {
        $t = $this->CI->site_theme;
        for ($i = 0; $i < 16 && $t->contrast($hex, $against) < $ratio; $i++) {
            $hex = $t->mix($hex, '#000000', 0.12);
        }
        return $hex;
    }

    // ------------------------------------------------------------------ slide images

    /**
     * Stores an uploaded slide picture (jpg, png or webp, up to 5 MB) in the platform's uploads and
     * returns its path ("uploads/gallery/media/slide_<hex>.jpg").
     * @throws RuntimeException with a message for the admin
     */
    public function store_image(array $file)
    {
        if (!isset($file['tmp_name'], $file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Choose a picture to upload.');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('The picture is larger than 5 MB. Use a smaller one.');
        }
        $info = @getimagesize($file['tmp_name']);
        $types = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp');
        if (!$info || !isset($types[$info[2]])) {
            throw new RuntimeException('Upload a JPG, PNG or WebP picture.');
        }
        $path = self::IMAGE_DIR . 'slide_' . bin2hex(random_bytes(8)) . '.' . $types[$info[2]];
        $this->CI->load->helper('upload');
        $dest = upload_fs_path($path);
        if (!is_dir(dirname($dest))) {
            @mkdir(dirname($dest), 0775, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('The picture could not be saved. Try again.');
        }
        return $path;
    }
}
