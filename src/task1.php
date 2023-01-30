<?php

// region task1
$a = [1, 2, 3];
$s = 'test';
$o = [
    'a' => 1,
    'b' => 2,
    'c' => 3,
];


if (count($a)    > 0) echo 'Error';

if (count($a) > 0) {
    echo 'Error';
}


if (count($s) > 0) echo 'Error';

if (count($s) > 0) {
    echo 'Error';
}


if (count($o) > 0) echo 'Error';

if (count($o) > 0) {
    echo 'Error';
}



if (count($a)) {
    echo 'Pass';
}

// endregion
