<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Finds questions already in the question bank: the same question (same words, ignoring case,
 * punctuation, spacing and formatting) or a very similar one (90% alike) in the same subject.
 */
class Question_dedupe
{
    const SIMILAR = 90;

    private $CI;
    private $cache = array();

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public static function normalize($html)
    {
        $t = html_entity_decode(strip_tags(str_ireplace(array('<br>', '<br/>', '<br />', '</p>'), ' ', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = mb_strtolower($t, 'UTF-8');
        $t = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $t);
        return trim(preg_replace('/\s+/', ' ', $t));
    }

    /** The subject's questions: [id => normalized text]. */
    private function bank($subject_id)
    {
        $subject_id = (int) $subject_id;
        if (!isset($this->cache[$subject_id])) {
            $this->cache[$subject_id] = array();
            foreach ($this->CI->db->select('id, question')->where('subject_id', $subject_id)->get('questions')->result_array() as $r) {
                $this->cache[$subject_id][(int) $r['id']] = self::normalize($r['question']);
            }
        }
        return $this->cache[$subject_id];
    }

    /** Remember a question added in this request (bulk adds), so later rows are compared with it. */
    public function remember($subject_id, $id, $html)
    {
        $this->bank($subject_id);
        $this->cache[(int) $subject_id][$id ?: 'new' . count($this->cache[(int) $subject_id])] = self::normalize($html);
    }

    /**
     * array(kind, id, percent) for the closest existing question: kind 'same' or 'similar'; null when
     * none. $exclude_id: the question being edited.
     */
    public function find($subject_id, $html, $exclude_id = 0)
    {
        $text = self::normalize($html);
        if ($text === '') {
            return null;
        }
        $best = null;
        $len = strlen($text);
        foreach ($this->bank($subject_id) as $id => $other) {
            if ((string) $id === (string) $exclude_id || $other === '') {
                continue;
            }
            if ($other === $text) {
                return array('same', $id, 100);
            }
            $olen = strlen($other);
            if ($len < 20 || abs($olen - $len) > max($len, $olen) * 0.15 || $len > 2000) {
                continue;
            }
            similar_text($text, $other, $pct);
            if ($pct >= self::SIMILAR && (!$best || $pct > $best[2])) {
                $best = array('similar', $id, round($pct));
            }
        }
        return $best;
    }
}
