<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Parse mysqldump: returns [table => ['ddl'=>..., 'cols'=>[name=>def], 'keys'=>[...], 'fks'=>[...], 'opts'=>...]]
function parse_dump($file) {
    $sql = file_get_contents($file);
    preg_match_all('/^CREATE TABLE `([^`]+)` \((.*?)\n\) (ENGINE=[^;]*);/ms', $sql, $m, PREG_SET_ORDER);
    $t = [];
    foreach ($m as $x) {
        $cols = []; $keys = []; $fks = [];
        foreach (explode("\n", trim($x[2])) as $line) {
            $line = rtrim(trim($line), ',');
            if (preg_match('/^`([^`]+)` (.*)$/', $line, $c)) $cols[$c[1]] = $c[2];
            elseif (preg_match('/^CONSTRAINT `([^`]+)` FOREIGN KEY \(([^)]+)\) REFERENCES `([^`]+)` \(([^)]+)\)(.*)$/', $line, $f))
                $fks[] = ['name'=>$f[1],'cols'=>$f[2],'ref'=>$f[3],'refcols'=>$f[4],'rest'=>trim($f[5]),'line'=>$line];
            else $keys[] = $line;
        }
        $t[$x[1]] = ['ddl'=>$x[0],'cols'=>$cols,'keys'=>$keys,'fks'=>$fks,'opts'=>$x[3]];
    }
    return $t;
}
