<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Import files (students, staff, library books, exam marks): an Excel (.xlsx) sample to download,
 * with the heading row the import reads and one filled-in example row, and reading an uploaded
 * Excel or CSV file the same way.
 *
 * The heading names are the ones each import reads (Student/Staff/Book read them by name or
 * position), so keep them as they are. Every column of the sample is formatted as text, so Excel
 * keeps leading zeros in phone numbers and dates typed as 2015-03-14; dates Excel still turns into
 * date numbers are turned back into Y-m-d on import.
 */
class Import_sheet
{
    const XLSX_MIMES = array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream');
    const CSV_MIMES = array('text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values', 'application/excel',
        'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext', 'application/octet-stream', 'application/txt', 'text/tsv');
    const MAX_BYTES = 10485760;

    /** name => array(file name, headings, example row) */
    public static function templates()
    {
        return array(
            'student' => array('student_import_sample.xlsx',
                array('admission_no', 'roll_no', 'first_name', 'middlename', 'last_name', 'gender', 'date_of_birth', 'category', 'religion', 'caste',
                    'mobile_no', 'email', 'admission_date', 'blood_group', 'student_house', 'height', 'weight', 'measurement_date', 'father_name',
                    'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name',
                    'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address',
                    'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'national_identification_no', 'local_identification_no',
                    'rte', 'previous_school', 'note'),
                array('', '1', 'Chinedu', 'Emeka', 'Okafor', 'Male', '2015-03-14', '', 'Christianity', '',
                    '08031234567', 'chinedu.okafor@example.com', '2025-09-15', 'O+', '', '120 cm', '30 kg', '', 'Emeka Okafor',
                    '08031234567', 'Engineer', 'Ngozi Okafor', '08059876543', 'Teacher', 'Father', 'Emeka Okafor',
                    'Father', 'emeka.okafor@example.com', '08031234567', 'Engineer', '12 Allen Avenue, Ikeja, Lagos', '12 Allen Avenue, Ikeja, Lagos',
                    '12 Allen Avenue, Ikeja, Lagos', '', '', '', '', '',
                    '', 'Bright Stars Nursery School', '')),
            'staff' => array('staff_import_sample.xlsx',
                array('employee_id', 'qualification', 'work_exp', 'name', 'surname', 'father_name', 'mother_name', 'contact_no', 'emergency_contact_no',
                    'email', 'dob', 'marital_status', 'date_of_joining', 'date_of_leaving', 'local_address', 'permanent_address', 'note', 'gender',
                    'account_title', 'bank_account_no', 'bank_name', 'ifsc_code', 'bank_branch', 'payscale', 'basic_salary', 'epf_no', 'contract_type',
                    'shift', 'location', 'facebook', 'twitter', 'linkedin', 'instagram', 'resume', 'joining_letter', 'resignation_letter'),
                array('EMP001', 'B.Ed', '3 years', 'Amaka', 'Nwosu', 'Peter Nwosu', 'Grace Nwosu', '08023456789', '08034567890',
                    'amaka.nwosu@example.com', '1990-05-20', 'Married', '2024-09-01', '', '5 Ojo Road, Lagos', '5 Ojo Road, Lagos', '', 'Female',
                    'Amaka Nwosu', '0123456789', 'GTBank', '', 'Ikeja', '', '150000', '', 'Permanent',
                    'Morning', 'Main campus', '', '', '', '', '', '', '')),
            'book' => array('book_import_sample.xlsx',
                array('book_title', 'book_no', 'isbn_no', 'subject', 'rack_no', 'publish', 'author', 'qty', 'perunitcost', 'postdate', 'description', 'available'),
                array('Things Fall Apart', 'LIB-0001', '9780385474542', 'Literature in English', 'A3', 'Heinemann', 'Chinua Achebe', '5', '3500', '2025-01-10', 'Novel', 'yes')),
            'marks' => array('marks_import_sample.xlsx',
                array('adm_no', 'status (1 present, 0 absent)', 'marks', 'note'),
                array('1001', '1', '75', 'Good')),
        );
    }

    /** Sends the Excel sample for one import. */
    public function download($name)
    {
        $t = self::templates();
        if (!isset($t[$name])) {
            show_404();
        }
        list($file, $headings, $example) = $t[$name];
        $CI = &get_instance();
        $CI->load->helper('download');
        if (!class_exists('ZipArchive')) { // no zip support on this server: the same two rows as CSV
            $f = fopen('php://temp', 'r+');
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, $headings, ',', '"', '');
            fputcsv($f, $example, ',', '"', '');
            rewind($f);
            return force_download(preg_replace('/\.xlsx$/', '.csv', $file), stream_get_contents($f));
        }
        force_download($file, self::xlsx($headings, $example));
    }

    /**
     * Checks an uploaded import file ($_FILES[$field]): '' when it is a CSV or Excel file of a sensible size,
     * otherwise the reason it can't be used.
     */
    public static function upload_problem($field = 'file')
    {
        if (empty($_FILES[$field]['name']) || empty($_FILES[$field]['size'])) {
            return 'Choose the file to import.';
        }
        $f = $_FILES[$field];
        if (!empty($f['error'])) {
            return 'The file could not be uploaded. Please try again.';
        }
        if ($f['size'] > self::MAX_BYTES) {
            return 'The file is too big (10 MB at most).';
        }
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv' && $ext !== 'xlsx') {
            return 'Upload the Excel (.xlsx) or CSV file. Older .xls files: open them in Excel and save as .xlsx first.';
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        $ok = $ext === 'xlsx' ? self::XLSX_MIMES : self::CSV_MIMES;
        if (!in_array($mime, $ok, true)) {
            return 'This file does not look like ' . ($ext === 'xlsx' ? 'an Excel' : 'a CSV') . ' file.';
        }
        return '';
    }

    /** All rows of an uploaded CSV or Excel file (first sheet), heading row included: array(rows, error). */
    public static function rows($path, $original_name)
    {
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if ($ext === 'csv') {
            $f = fopen($path, 'r');
            if (!$f) {
                return array(array(), 'The file could not be read.');
            }
            $rows = array();
            if (fread($f, 3) !== "\xEF\xBB\xBF") {
                rewind($f);
            }
            while (($row = fgetcsv($f, 0, ',', '"', '')) !== false) {
                $rows[] = $row;
            }
            fclose($f);
            return array($rows, null);
        }
        $CI = &get_instance();
        $CI->load->library('Question_import');
        return Question_import::read($path, $ext);
    }

    /**
     * The rows keyed by their heading, numbered from 1 and without empty rows, like CSVReader::parse_file:
     * array(1 => array('first_name' => 'Chinedu', ...), ...). Dates Excel turned into date numbers come back as Y-m-d.
     */
    public static function keyed_rows($path, $original_name)
    {
        list($rows, $error) = self::rows($path, $original_name);
        if ($error || empty($rows)) {
            return array();
        }
        $headings = array_map(function ($h) { return trim((string) $h); }, array_shift($rows));
        $out = array();
        $i = 1;
        foreach ($rows as $cells) {
            $cells = array_slice(array_pad($cells, count($headings), ''), 0, count($headings));
            if (trim(implode('', $cells)) === '') {
                continue;
            }
            $row = array();
            foreach ($headings as $k => $h) {
                if ($h === '') {
                    continue;
                }
                $v = (string) $cells[$k];
                if (self::is_date_heading($h) && preg_match('/^\d{4,5}(\.\d+)?$/', $v)) {
                    $v = gmdate('Y-m-d', (int) round(((float) $v - 25569) * 86400)); // Excel date number
                }
                $row[$h] = $v;
            }
            $out[$i++] = $row;
        }
        return $out;
    }

    private static function is_date_heading($h)
    {
        return $h === 'dob' || strpos($h, 'date') !== false;
    }

    /** A one-sheet .xlsx (as a string): bold heading row, then the example row (and any $more rows); every column formatted as text. */
    public static function xlsx(array $headings, array $example, array $more = array())
    {
        $esc = function ($s) { return htmlspecialchars((string) $s, ENT_XML1 | ENT_QUOTES, 'UTF-8'); };
        $col = function ($n) { $s = ''; for ($n++; $n > 0; $n = intdiv($n - 1, 26)) { $s = chr(65 + ($n - 1) % 26) . $s; } return $s; };
        $cols = '';
        foreach ($headings as $k => $h) {
            $w = min(40, max(12, strlen($h) + 4, strlen((string) (isset($example[$k]) ? $example[$k] : '')) + 4));
            $cols .= '<col min="' . ($k + 1) . '" max="' . ($k + 1) . '" width="' . $w . '" style="1" customWidth="1"/>';
        }
        $sheet_rows = '';
        foreach (array_merge(array($headings, $example), $more) as $r => $cells) {
            $sheet_rows .= '<row r="' . ($r + 1) . '">';
            foreach ($headings as $k => $unused) {
                $v = isset($cells[$k]) ? $cells[$k] : '';
                $sheet_rows .= '<c r="' . $col($k) . ($r + 1) . '" s="' . ($r === 0 ? 2 : 1) . '" t="inlineStr"><is><t xml:space="preserve">' . $esc($v) . '</t></is></c>';
            }
            $sheet_rows .= '</row>';
        }
        $parts = array(
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Import" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFDDEBF7"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/><xf numFmtId="49" fontId="1" fillId="2" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1"/></cellXfs></styleSheet>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols>' . $cols . '</cols><sheetData>' . $sheet_rows . '</sheetData></worksheet>',
        );
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);
        foreach ($parts as $name => $xml) {
            $zip->addFromString($name, $xml);
        }
        $zip->close();
        $bytes = file_get_contents($tmp);
        @unlink($tmp);
        return $bytes;
    }
}
