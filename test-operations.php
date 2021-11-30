<?php
/**
 * test-operations.php
 *
 * Tests for the maximum number of operators in a file by brute force.
 *
 * @package
 * @author: Samuel Levy <sam@determineddevelopment.com>
 */

$fname = __DIR__ . '/out/operations.php';
$php = 'php8.1 -d memory_limit=128M';

for ($rounds = 1 ;; $rounds ++) {
    $fp = fopen($fname, 'w');
    fwrite($fp, '<?php $a = ');
    fwrite($fp, str_repeat('!', $rounds));
    fwrite($fp, 'true; echo "done";');
    fclose($fp);

    $out = shell_exec($php . ' ' . $fname);

    if (! str_starts_with($out, 'done')) {
        echo 'Failed at ' . $rounds . ' operations' . "\n";
        break;
    }
}

echo 'Completed testing!' . "\n";