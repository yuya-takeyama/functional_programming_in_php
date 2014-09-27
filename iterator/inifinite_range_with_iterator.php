<?php
require_once __DIR__ . '/Functional.php';
use Yuyat\Functional\WithIterator as F;

foreach (F\range(1, INF) as $n) {
    echo $n, PHP_EOL;
}
