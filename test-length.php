<?php
/**
 * test-length.php
 *
 * Tests for the maximum length of a string by brute force.
 *
 * @package
 * @author: Samuel Levy <sam@determineddevelopment.com>
 */

$fname = __DIR__ . '/out/length.php';
$php = 'php8.1 -d memory_limit=128M';
$step = 1048576;

for ($rounds = 1 ;; $rounds ++) {
    $fp = fopen($fname, 'w');
    fwrite($fp, '<?php $a = "');
    for ($r = 1; $r <= $rounds; $r ++) {
        fwrite($fp, str_repeat('a', $step));
    }
    fwrite($fp, '"; echo "done " . strlen($a);');
    fclose($fp);

    $out = shell_exec($php . ' ' . $fname);

    if (! str_starts_with($out, 'done')) {
        echo 'Failed at ' . ($rounds * $step) . ' characters' . "\n";
        break;
    }
}

echo 'Completed testing!' . "\n";