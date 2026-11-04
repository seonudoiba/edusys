<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Parse INSERT rows from a mysqldump file.
// Returns [table => [ [v1, v2, ...], ... ]]; NULL -> null, everything else -> string.
function parse_rows($file) {
    $out = [];
    $bs = chr(92); // backslash
    $map = ['n' => "\n", 'r' => "\r", 't' => "\t", '0' => "\0", 'Z' => "\x1a", 'b' => "\x08"];
    $fh = fopen($file, 'r');
    while (($line = fgets($fh)) !== false) {
        if (strncmp($line, 'INSERT INTO `', 13) !== 0) continue;
        $p = strpos($line, '`', 13);
        $table = substr($line, 13, $p - 13);
        $i = strpos($line, ' VALUES ', $p) + 8;
        $n = strlen($line);
        while ($i < $n) {
            if ($line[$i] !== '(') { $i++; continue; }
            $i++;
            $row = [];
            while (true) {
                $c = $line[$i];
                if ($c === "'") {
                    $i++;
                    $s = '';
                    while (true) {
                        $c = $line[$i];
                        if ($c === $bs) {
                            $e = $line[$i + 1];
                            $s .= $map[$e] ?? $e;
                            $i += 2;
                            continue;
                        }
                        if ($c === "'") {
                            if (($line[$i + 1] ?? '') === "'") { $s .= "'"; $i += 2; continue; }
                            $i++;
                            break;
                        }
                        $j = strcspn($line, $bs . "'", $i);
                        $s .= substr($line, $i, $j);
                        $i += $j;
                    }
                    $row[] = $s;
                } else {
                    $j = strcspn($line, ',)', $i);
                    $tok = substr($line, $i, $j);
                    $i += $j;
                    if ($tok === 'NULL') $row[] = null;
                    elseif (strncmp($tok, '_binary ', 8) === 0) throw new Exception("binary literal in $table");
                    elseif (strncmp($tok, '0x', 2) === 0) $row[] = hex2bin(substr($tok, 2));
                    else $row[] = $tok;
                }
                $c = $line[$i];
                $i++;
                if ($c === ')') break;
            }
            $out[$table][] = $row;
        }
    }
    fclose($fh);
    return $out;
}
