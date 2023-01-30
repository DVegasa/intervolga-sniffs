<?php
namespace PHP_CodeSniffer\standards\DVegasa\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DVegasaTestSniff implements Sniff {

    public function register() {
        return [T_COMMENT];
    }

    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['content']{0} === '#') {
            $error = 'Hashes are banned';
            $data = [$tokens[$stackPtr]['content']];
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }
    }
}
