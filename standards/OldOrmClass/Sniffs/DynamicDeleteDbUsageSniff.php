<?php

namespace PHP_CodeSniffer\standards\OldOrmClass\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DynamicDeleteDbUsageSniff implements Sniff
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

        if (strtolower($funcName['content']) !== 'delete') {
            return;
        }

        // Проверка на статику
        if (
            $tokens[$stackPtr - 1]['code'] === T_STATIC
            || $tokens[$stackPtr - 2]['code'] === T_STATIC
        ) {
            $phpcsFile->addError('Method Delete should be dynamic', $stackPtr, 'DynamicDeleteDbUsageSniff');
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
            $phpcsFile->addError('Method Delete should use global variable $DB', $stackPtr, 'DynamicDeleteDbUsageSniff');
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
            $phpcsFile->addError('Method Delete should use $DB->Query()', $stackPtr, 'DynamicDeleteDbUsageSniff');
        }
    }
}
