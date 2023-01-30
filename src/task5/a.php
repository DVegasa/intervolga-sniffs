<?php

// В отдельном стандарте "OldOrmClass": метод Delete должен быть динамическим, содержать использование глобальной переменной $DB и вызов $DB->Query()


class A {
    public function Delete() {
        global $DB;
        $DB->Query();
    }
}

class B {
    function Delete() {
        $DB = [
            'Query' => function() {}
        ];
        $DB->Query();
    }
}

class C {
    public static function Delete() {
        global $DB;
        $DB->Query();
    }
}


class D {
    public function Delete() {
        global $DB;
        $DB->query();
    }
}


class E {
    public function Delete() {
        global $DB;
//        $DB->Query();
    }
}
