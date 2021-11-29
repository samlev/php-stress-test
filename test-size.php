<?php
/**
 * test-size.php
 *
 * @package
 * @author: Samuel Levy <sam@determineddevelopment.com>
 */
$fname = __DIR__ . '/out/size.php';
$php = 'php7.3 -d memory_limit=128M';
$step = 1048576;

for ($rounds = 1 ;; $rounds ++) {
    $fp = fopen($fname, 'w');
    fwrite($fp, '<?php /');
    for ($r = 1; $r <= $rounds; $r ++) {
        fwrite($fp, str_repeat('*', $step));
    }
    fwrite($fp, '/ echo "done";');
    fclose($fp);

    $out = shell_exec($php . ' ' . $fname);

    if (! str_starts_with($out, 'done')) {
        echo 'Failed at ' . ($rounds * $step) . ' characters' . "\n";
        break;
    }
}

echo 'Completed testing!' . "\n";