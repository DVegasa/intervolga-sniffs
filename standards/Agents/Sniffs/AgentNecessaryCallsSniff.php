<?php
namespace PHP_CodeSniffer\standards\Agents\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/*
 * CModule::IncludeModule,
 * CIBlockElement::GetList,
 * CEventLog::Add,
 * CUser::GetList,
 * CEvent::Send,
 */

class AgentNecessaryCallsSniff implements Sniff {

    public function register() {
        return [T_FUNCTION];
    }

//    private bool $launches = false;

    public function process(File $phpcsFile, $stackPtr) {
//        $this->launches++;
//        if ($this->launches >= 2) return;

        $tokens = $phpcsFile->getTokens();

        $function = $tokens[$stackPtr];

        $from = $stackPtr;
        $correct = false;

        while (true) {
            $posDoubleColon = $phpcsFile->findNext(T_DOUBLE_COLON, $from, $function['scope_closer']);
            if (!$posDoubleColon) break;
            $from = $posDoubleColon + 1;

            $tokenBefore = $tokens[$posDoubleColon - 1];
            $tokenAfter = $tokens[$posDoubleColon + 1];

//            print_r($tokenBefore);
//            print_r(PHP_EOL);
//            print_r($tokenAfter);

            if (
                ($tokenBefore['content'] === 'CModule' && $tokenAfter['content'] === 'IncludeModule') ||
                ($tokenBefore['content'] === 'CIBlockElement' && $tokenAfter['content'] === 'GetList') ||
                ($tokenBefore['content'] === 'CEventLog' && $tokenAfter['content'] === 'Add') ||
                ($tokenBefore['content'] === 'CUser' && $tokenAfter['content'] === 'GetList') ||
                ($tokenBefore['content'] === 'CEvent' && $tokenAfter['content'] === 'Send')
            ) {
                $nextToken = $tokens[$posDoubleColon + 2];
                print_r($nextToken);

                if ($nextToken['type'] === 'T_OPEN_PARENTHESIS') {
                    $correct = true;
                    return;
                }
            }
        }

        if ($correct === false) {
            $phpcsFile->addError('Every function must contain CModule::IncludeModule, CIBlockElement::GetList, CEventLog::Add, CUser::GetList, CEvent::Send', $stackPtr, 'AgentNecessaryCallsSniff');
        }
    }
}
