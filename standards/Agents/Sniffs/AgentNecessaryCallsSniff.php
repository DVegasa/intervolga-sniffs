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

    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        $function = $tokens[$stackPtr];

        $from = $stackPtr;
        $correct = false;

        $calls = [
            'cmodule' => false,
            'ciblockelement' => false,
            'ceventlog' => false,
            'cuser' => false,
            'cevent' => false,
        ];

        while (true) {
            $posDoubleColon = $phpcsFile->findNext(T_DOUBLE_COLON, $from, $function['scope_closer']);
            if (!$posDoubleColon) break;
            $from = $posDoubleColon + 1;

            $tokenBefore = $tokens[$posDoubleColon - 1];
            $tokenAfter = $tokens[$posDoubleColon + 1];



            if ($tokenBefore['content'] === 'CModule' && $tokenAfter['content'] === 'IncludeModule') {
                $nextToken = $tokens[$posDoubleColon + 2];

                if ($nextToken['type'] === 'T_OPEN_PARENTHESIS') {
                    $calls['cmodule'] = true;
                }
            }

            if ($tokenBefore['content'] === 'CIBlockElement' && $tokenAfter['content'] === 'GetList') {
                $nextToken = $tokens[$posDoubleColon + 2];

                if ($nextToken['type'] === 'T_OPEN_PARENTHESIS') {
                    $calls['ciblockelement'] = true;
                }
            }

            if ($tokenBefore['content'] === 'CEventLog' && $tokenAfter['content'] === 'Add') {
                $nextToken = $tokens[$posDoubleColon + 2];

                if ($nextToken['type'] === 'T_OPEN_PARENTHESIS') {
                    $calls['ceventlog'] = true;
                }
            }

            if ($tokenBefore['content'] === 'CUser' && $tokenAfter['content'] === 'GetList') {
                $nextToken = $tokens[$posDoubleColon + 2];

                if ($nextToken['type'] === 'T_OPEN_PARENTHESIS') {
                    $calls['cuser'] = true;
                }
            }

            if ($tokenBefore['content'] === 'CEvent' && $tokenAfter['content'] === 'Send') {
                $nextToken = $tokens[$posDoubleColon + 2];

                if ($nextToken['type'] === 'T_OPEN_PARENTHESIS') {
                    $calls['cevent'] = true;
                }
            }
        }


        if (in_array(false, $calls)) {
            $phpcsFile->addError('Every function must contain CModule::IncludeModule, CIBlockElement::GetList, CEventLog::Add, CUser::GetList, CEvent::Send', $stackPtr, 'AgentNecessaryCallsSniff');
        }
    }
}
