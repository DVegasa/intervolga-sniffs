<?php
namespace PHP_CodeSniffer\standards\intervolga\Sniffs;


use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/* if (count($a) > 0) */

class BadCountInsideIfSniff implements Sniff {

    public function register() {
        return [T_IF];
    }

    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        $if = $tokens[$stackPtr]; // if
        $stackPtr += $if['length'];

        $posOpenP = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        $openP = $tokens[$posOpenP];

        $posStmt = $phpcsFile->findStartOfStatement($posOpenP+1);
        $stmt = $tokens[$posStmt];
        if ($stmt['content'] !== 'count') return;

        $posCloseP = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $posStmt);
        $closeP = $tokens[$posCloseP];

        $str = ($phpcsFile->getTokensAsString($posCloseP+1, $if['parenthesis_closer'] - $posCloseP + 1));

        $str = preg_replace('/\s+/', '', $str);

        if ($str === '>0)') {
            $error = 'if (count(...)>0) is prohibited. Use if (count(...)) instead.';
            $data = [$tokens[$stackPtr]['content']];
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }
    }
}
