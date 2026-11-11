<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "third_party/omnipay/vendor/autoload.php";

use Mpdf\Mpdf;

class M_pdf
{

    public function __construct()
    {
        $CI = &get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }

    public function load($param = NULL)
    {
        if($param ==NULL){
            return  new Mpdf([
                'tempDir' => __DIR__ . '/../tmp',
                'mode' => 'utf-8',
                'default_font' => 'roboto',
                'margin_left' => 2,
                'margin_right' => 2,
                'margin_top' => 2,
                'margin_bottom' => 2,
                'format' => 'Legal'
            ]);
        }
        // An orientation ('P' portrait, 'L' landscape: the CBSE Examination templates) on A4.
        // mPDF 7+ takes only a settings array; a plain string here used to stop the report printing.
        if (is_string($param)) {
            $orientation = strtoupper(substr(trim($param), 0, 1)) === 'L' ? 'L' : 'P';
            return new Mpdf([
                'tempDir' => __DIR__ . '/../tmp',
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => $orientation,
            ]);
        }
        return new Mpdf($param + ['tempDir' => __DIR__ . '/../tmp']);
      
    }
}
