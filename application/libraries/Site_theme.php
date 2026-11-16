<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The school website's design: which theme (a preset in config/site_themes.php) and the school's own
 * changes to it, turned into CSS variables, a Google Fonts link and body classes for
 * views/themes/modern. Colours for text on coloured backgrounds are worked out here, so whatever
 * colour a school picks, text stays readable.
 */
class Site_theme
{
    const ENGINE = 'modern';

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('site_themes');
    }

    public function themes()
    {
        return $this->CI->config->item('site_themes');
    }

    public function fonts()
    {
        return $this->CI->config->item('site_theme_fonts');
    }

    public function fields()
    {
        return $this->CI->config->item('site_theme_options');
    }

    /** True when the key is one of the website themes (not the platform's own design). */
    public function is_theme($key)
    {
        $themes = $this->themes();
        return is_string($key) && isset($themes[$key]);
    }

    /**
     * The options in effect: the theme's preset, with the school's saved changes on top.
     * @param object $front_setting front_cms_settings row
     * @param array|null $override unsaved options to try (the design page's live preview)
     */
    public function options($front_setting, array $override = null)
    {
        $themes = $this->themes();
        $key = isset($front_setting->theme) && isset($themes[$front_setting->theme]) ? $front_setting->theme : 'default';
        $options = $themes[$key]['options'];
        $saved = isset($front_setting->theme_options) ? json_decode((string) $front_setting->theme_options, true) : null;
        if (is_array($saved) && isset($saved['theme'], $saved['options']) && $saved['theme'] === $key) {
            $options = array_merge($options, $this->clean($saved['options']));
        }
        if ($override !== null) {
            if (isset($override['theme']) && isset($themes[$override['theme']])) {
                $key = $override['theme'];
                $options = $themes[$key]['options'];
            }
            $options = array_merge($options, $this->clean(isset($override['options']) ? $override['options'] : array()));
        }
        $options['theme'] = $key;
        return $options;
    }

    /** Only known options with allowed values; anything else is dropped. */
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
            switch ($f['type']) {
                case 'color':
                    if (preg_match('/^#[0-9a-fA-F]{6}$/', $v)) {
                        $out[$name] = strtolower($v);
                    }
                    break;
                case 'font':
                    if (isset($fonts[$v])) {
                        $out[$name] = $v;
                    }
                    break;
                case 'select':
                    if (isset($f['choices'][$v])) {
                        $out[$name] = $v;
                    }
                    break;
                case 'check':
                    $out[$name] = $v === '1' || $v === 'on' || $v === 'true' ? 1 : 0;
                    break;
                case 'text':
                    $out[$name] = mb_substr(trim(strip_tags($v)), 0, isset($f['max']) ? $f['max'] : 200);
                    break;
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ output

    /** The CSS variables for the page. */
    public function css(array $o)
    {
        $fonts = $this->fonts();
        $primary = $o['primary'];
        $accent = $o['accent'];
        $dark = $this->mix($primary, '#000000', 0.78);
        $vars = array(
            '--c-primary' => $primary,
            '--c-primary-ink' => $this->ink($primary),
            '--c-primary-strong' => $this->mix($primary, '#000000', 0.18),
            '--c-primary-tint' => $this->mix($primary, '#ffffff', 0.92),
            '--c-primary-soft' => $this->mix($primary, '#ffffff', 0.8),
            '--c-link' => $this->readable_on_white($primary),
            '--c-accent' => $accent,
            '--c-accent-ink' => $this->ink($accent),
            '--c-dark' => $dark,
            '--c-dark-ink' => '#ffffff',
            '--c-topbar' => $this->mix($primary, '#000000', 0.55),
            '--font-head' => $fonts[$o['heading_font']][2],
            '--font-body' => $fonts[$o['body_font']][2],
            '--text-base' => array('s' => '15px', 'm' => '16px', 'l' => '17.5px')[$o['text_size']],
            '--radius' => array('square' => '0px', 'soft' => '6px', 'round' => '14px')[$o['corners']],
            '--radius-btn' => $o['buttons'] === 'pill' ? '999px' : array('square' => '0px', 'soft' => '6px', 'round' => '12px')[$o['corners']],
            '--container' => $o['width'] === 'wide' ? '1320px' : '1170px',
        );
        // Header and footer colours from their style.
        $header = array('light' => array('#ffffff', '#16202b'), 'brand' => array($primary, $this->ink($primary)), 'dark' => array($dark, '#ffffff'));
        $footer = array('light' => array($this->mix($primary, '#ffffff', 0.95), '#16202b'), 'brand' => array($this->mix($primary, '#000000', 0.12), $this->ink($this->mix($primary, '#000000', 0.12))), 'dark' => array($this->mix($primary, '#000000', 0.84), '#ffffff'));
        list($vars['--c-header'], $vars['--c-header-ink']) = $header[$o['header_style']];
        list($vars['--c-footer'], $vars['--c-footer-ink']) = $footer[$o['footer']];
        $css = ':root{';
        foreach ($vars as $k => $v) {
            $css .= $k . ':' . $v . ';';
        }
        return $css . '}';
    }

    /** The Google Fonts stylesheet for the two fonts in use. */
    public function fonts_url(array $o)
    {
        $fonts = $this->fonts();
        $families = array_unique(array($fonts[$o['heading_font']][1], $fonts[$o['body_font']][1]));
        return 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $families) . '&display=swap';
    }

    public function body_classes(array $o)
    {
        return implode(' ', array(
            'st-hdr-' . $o['header'], 'st-hdr-' . $o['header_style'], 'st-menu-' . $o['menu_style'], 'st-btn-' . $o['buttons'],
            'st-ftr-' . $o['footer'], 'st-corners-' . $o['corners'], 'st-banner-' . $o['hero'], $o['sticky'] ? 'st-sticky' : '',
        ));
    }

    // ------------------------------------------------------------------ colours

    private function rgb($hex)
    {
        $hex = ltrim($hex, '#');
        return array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
    }

    /** $a mixed with $b; $amount of $b from 0 to 1. */
    public function mix($a, $b, $amount)
    {
        $x = $this->rgb($a);
        $y = $this->rgb($b);
        $out = '#';
        for ($i = 0; $i < 3; $i++) {
            $out .= str_pad(dechex((int) round($x[$i] + ($y[$i] - $x[$i]) * $amount)), 2, '0', STR_PAD_LEFT);
        }
        return $out;
    }

    private function luminance($hex)
    {
        $l = array();
        foreach ($this->rgb($hex) as $c) {
            $c /= 255;
            $l[] = $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
        }
        return 0.2126 * $l[0] + 0.7152 * $l[1] + 0.0722 * $l[2];
    }

    public function contrast($a, $b)
    {
        $la = $this->luminance($a);
        $lb = $this->luminance($b);
        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    /** White or near-black, whichever reads better on the colour. */
    public function ink($bg)
    {
        return $this->contrast($bg, '#ffffff') >= $this->contrast($bg, '#16202b') ? '#ffffff' : '#16202b';
    }

    /** The colour itself for links on white, darkened until it is readable (contrast 4.5:1). */
    private function readable_on_white($hex)
    {
        $c = $hex;
        for ($i = 0; $i < 12 && $this->contrast($c, '#ffffff') < 4.5; $i++) {
            $c = $this->mix($c, '#000000', 0.12);
        }
        return $c;
    }
}
