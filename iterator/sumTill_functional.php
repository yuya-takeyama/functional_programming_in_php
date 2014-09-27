<?php
require_once __DIR__ . '/Functional.php';
use Yuyat\Functional\WithIterator as F;

function sumTill($n) {
    return F\foldl(function ($x, $y) { return $x + $y; }, 0, F\take($n, F\range(1, INF)));
}

printf("SUM(1..100) = %d\n", sumTill(100));
