<?php
function sumTill($n) {
    $sum = 0;

    for ($i = 1; $i <= $n; $i++) {
        $sum += $i;
    }

    return $sum;
}

printf("SUM(1..100) = %d\n", sumTill(100));
