<?php
namespace PHP_CodeSniffer\standards\Agents\Sniffs;


use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;


class FunctionEndsWithReturnFunctionDotSniff implements Sniff {

    public function register() {
        return [T_RETURN, T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $x = $tokens[$stackPtr];

        if ($x['type'] == 'T_RETURN') {
            $this->everyReturnHasFunctionDot($phpcsFile, $stackPtr);
        } else {
            $this->everyFunctionHasReturn($phpcsFile, $stackPtr);
        }
    }


    private function everyFunctionHasReturn(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $function = $tokens[$stackPtr];

        $next = $phpcsFile->findNext(T_RETURN, $stackPtr, $function['scope_closer']);
        if ($next === false) {
            $phpcsFile->addError('Every function must contain return __FUNCTION__ . "(...);";', $stackPtr, 'FunctionEndsWithReturnFunctionDotSniff');
        }
    }

    private function everyReturnHasFunctionDot(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $return = $tokens[$stackPtr];

        $posEnd = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);

        $str = $phpcsFile->getTokensAsString($stackPtr+1, $posEnd - $stackPtr-1);
        // То, что возвращается из reutrn. Без слова return и без ; в конце строки
        $str = preg_replace('/\s+/', '', $str);

        $regex = <<<REGEX
/^__FUNCTION__\.['"]\(.*\);['"]$/
REGEX;

        if (!preg_match($regex, $str)) {
            $phpcsFile->addError('Every return must contain return __FUNCTION__ . "(...);";', $stackPtr, 'FunctionEndsWithReturnFunctionDotSniff');
        }
    }
}
