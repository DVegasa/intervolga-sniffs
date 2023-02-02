<?php

//В отдельном стандарте "Agents": любая функция, объявленная в файле, должна содержать вызовы методов: CModule::IncludeModule, CIBlockElement::GetList, CEventLog::Add, CUser::GetList, CEvent::Send

function a() {
    CModule::IncludeModule();
    CModule::IncludeModule();
    CIBlockElement::GetList();
    CEventLog::Add();
    CUser::GetList();
    CEvent::Send();
    return __FUNCTION__ . '();';
}


function b() {
    CIBlockElement::GetList();
    return __FUNCTION__ . "(b, c);";
}


function c() {
    return __FUNCTION__ . "(b, c);";
}


function d() {
    // CEvent::Send
    'CEvent::Send';
    <<<TEXT
CEvent::Send
TEXT;
    CUser::GetList;
    return __FUNCTION__ . "(b, c);";
}
