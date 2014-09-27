<?php
printf("Initial memory usage = %.2fMB\n", round(memory_get_usage() / 1024 / 1024, 2));
$range = range(1, 100000);

foreach ($range as $n) {
    echo $n, PHP_EOL;
}

printf("Peak memory usage = %.2fMB\n", round(memory_get_peak_usage() / 1024 / 1024, 2));
