<?php

function a() {
    $a = 2+2;
    return __FUNCTION__ . '();';
}


function b() {
    $a = 2+2;
    if ($a < 2) {
        return __FUNCTION__ . '(a);';
    } else {
        return __FUNCTION__ . "(b, c);";
    }
}


function c() {
    $a = 2+2;
}


function d() {
    return 2+2;
}


function e() {
    $a = 2+2;
    'return';
}
