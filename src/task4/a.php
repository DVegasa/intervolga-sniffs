<?php


/*
В отдельном стандарте "Agents":
любая функция, объявленная в файле,
должна оборачивать CUser::GetList и CEvent::Send в большее число if,
чем CEventLog::Add и CIBlockElement::GetList
*/


// PASS
function a() {
    if (true){
        if (true) {
            if (true) {
                CUser::GetList();
            }
        }
    }

    if (true) {
        CEventLog::Add();
    }

    return __FUNCTION__ . "(...);";
}


// ERROR
function b() {
    if (true){
        CUser::GetList();
    }

    if (true) {
        CEventLog::Add();
    }

    return __FUNCTION__ . "(...);";
}


// ERROR
function c() {
    CUser::GetList();

    if (true) {
        if (true) CEventLog::Add();
        if (true) true;
        if (true) CIBlockElement::GetList();
    }

    return __FUNCTION__ . "(...);";
}

