<?php

// В отдельном стандарте "OldOrmClass": метод GetList должен быть статическим, содержать использование глобальной переменной $DB и вызов $DB->Query()


class A {
    public static function GetList() {
        global $DB;
        $DB->Query();
    }
}

class B {
    static function GetList() {
        $DB = [
            'Query' => function() {}
        ];
        $DB->Query();
    }
}

class C {
    static public function GetList() {
        global $DB;
        $DB->Query();
    }
}


class D {
    public static function GetList() {
        global $DB;
        $DB->query();
    }
}


class E {
    public static function GetList() {
        global $DB;
//        $DB->Query();
    }
}
