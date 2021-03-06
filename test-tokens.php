<?php
/**
 * test-tokens.php
 *
 * Tests for the maximum number of tokens in a file by brute force.
 *
 * @package
 * @author: Samuel Levy <sam@determineddevelopment.com>
 */

$fname = __DIR__ . '/out/tokens.php';
$php = 'php8.1 -d memory_limit=128M';
$step = 100;

for ($rounds = 1 ;; $rounds ++) {
    $fp = fopen($fname, 'w');
    fwrite($fp, '<?php ');
    for ($r = 1; $r <= $rounds; $r ++) {
        fwrite($fp, str_repeat('$a;', $step));
    }
    fwrite($fp, ' echo "done";');
    fclose($fp);

    $out = shell_exec($php . ' ' . $fname);

    if (! str_starts_with($out, 'done')) {
        echo 'Failed at ' . ($rounds * $step) . ' tokens' . "\n";
        break;
    }
}

echo 'Completed testing!' . "\n";