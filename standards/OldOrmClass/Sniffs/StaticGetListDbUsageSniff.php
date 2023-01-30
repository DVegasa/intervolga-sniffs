<?php

namespace PHP_CodeSniffer\standards\OldOrmClass\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StaticGetListDbUsageSniff implements Sniff
{

    public function register()
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $func = $tokens[$stackPtr];

        $funcName = $tokens[$stackPtr + 2];

        if (strtolower($funcName['content']) !== 'getlist') {
            return;
        }

        // Проверка на статику
        if (
            $tokens[$stackPtr - 2]['code'] !== T_STATIC
            && $tokens[$stackPtr - 4]['code'] !== T_STATIC
        ) {
            $phpcsFile->addError('Method GetList should be static', $stackPtr, 'StaticGetListDbUsageSniff');
        }

        // Проверка на использование глобальной переменной $DB
        $globalDb = false;
        $from = $stackPtr;
        while (true) {
            $posGlobal = $phpcsFile->findNext(T_GLOBAL, $from, $func['scope_closer']);
            if ($posGlobal === false) break;
            $from = $posGlobal + 1;

            if ($tokens[$posGlobal + 2]['content'] === '$DB') {
                $globalDb = true;
                break;
            }
        }

        if ($globalDb === false) {
            $phpcsFile->addError('Method GetList should use global variable $DB', $stackPtr, 'StaticGetListDbUsageSniff');
        }

        // Проверка на использование $DB->Query()
        $query = false;
        // $from оставляем таким же
        while (true) {
            $posArrow = $phpcsFile->findNext(T_OBJECT_OPERATOR, $from, $func['scope_closer']);
            if ($posArrow === false) break;
            $from = $posArrow + 1;

            if ($tokens[$posArrow + 1]['content'] === 'Query') {
                $query = true;
                break;
            }
        }

        if ($query === false) {
            $phpcsFile->addError('Method GetList should use $DB->Query()', $stackPtr, 'StaticGetListDbUsageSniff');
        }
    }
}
