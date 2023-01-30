<?php
namespace PHP_CodeSniffer\standards\Agents\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/*
В отдельном стандарте "Agents":
любая функция, объявленная в файле,
должна оборачивать CUser::GetList и CEvent::Send в большее число if,
чем CEventLog::Add и CIBlockElement::GetList
*/
class AgentWantsDeeperIfSniff implements Sniff {

    public function register() {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $function = $tokens[$stackPtr];


        $from = $stackPtr;
        $correct = false;

        $matches = [
            'cuser' => [],
            'cevent' => [],
            'ceventlog' =>  [],
            'ciblockelement' => [],
        ];

        // Находим позиции, где встречаются искомые функции
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
                $tokenBefore['content'] === 'CUser'
                && $tokenAfter['content'] === 'GetList'
                && $tokens[$posDoubleColon + 2]['type'] === 'T_OPEN_PARENTHESIS'
            ) {
                $matches['cuser'][] = ['pos' => $posDoubleColon, 'depth' => 0];
            }


            if (
                $tokenBefore['content'] === 'CEvent'
                && $tokenAfter['content'] === 'Send'
                && $tokens[$posDoubleColon + 2]['type'] === 'T_OPEN_PARENTHESIS'
            ) {
                $matches['cevent'][] = ['pos' => $posDoubleColon, 'depth' => 0];
            }


            if (
                $tokenBefore['content'] === 'CEventLog'
                && $tokenAfter['content'] === 'Add'
                && $tokens[$posDoubleColon + 2]['type'] === 'T_OPEN_PARENTHESIS'
            ) {
                $matches['ceventlog'][] = ['pos' => $posDoubleColon, 'depth' => 0];
            }


            if (
                $tokenBefore['content'] === 'CIBlockElement'
                && $tokenAfter['content'] === 'GetList'
                && $tokens[$posDoubleColon + 2]['type'] === 'T_OPEN_PARENTHESIS'
            )

                $matches['ciblockelement'][] = ['pos' => $posDoubleColon, 'depth' => 0];
        }
//        print_r($matches);


        // Собираем все if'ы, найденные в функции
        $from = $stackPtr;
        $ifs = [];
        while (true) {
            $posIf = $phpcsFile->findNext(T_IF, $from, $function['scope_closer']);
            if (!$posIf) break;
            $from = $posIf + 1;

            if (isset ($tokens[$posIf]['scope_closer'])) {
                $end = $tokens[$posIf]['scope_closer'];
            } else {
                $end = $phpcsFile->findNext(T_SEMICOLON, $posIf);
            }

            $ifs[] = [
                'start' => $posIf,
                'end' => $end,
            ];
        }

//        print_r($ifs);


        // Подсчитываем вложенности
        foreach ($ifs as $if) {
            foreach ($matches as $key => $match) {
                foreach ($match as $i => $m) {
                    if ($m['pos'] > $if['start'] && $m['pos'] < $if['end']) {
                        $matches[$key][$i]['depth']++;
                    }
                }
            }
        }

        print_r($matches);

        // Сравниваем глубины
        foreach ($matches['cuser'] as $slave) {
            foreach ($matches['ceventlog'] as $master) {
                if ($slave['depth'] <= $master['depth']) {
                    $phpcsFile->addError('CUser::GetList должен быть вложен в большее число if, чем CEventLog::Add', $stackPtr, 'AgentWantsDeeperIfSniff');
                }
            }

            foreach ($matches['ciblockelement'] as $master) {
                if ($slave['depth'] <= $master['depth']) {
                    $phpcsFile->addError('CUser::GetList должен быть вложен в большее число if, чем CIBlockElement::GetList', $stackPtr, 'AgentWantsDeeperIfSniff');
                }
            }
        }


        foreach ($matches['cevent'] as $slave) {
            foreach ($matches['ceventlog'] as $master) {
                if ($slave['depth'] <= $master['depth']) {
                    $phpcsFile->addError('CEvent::Send должен быть вложен в большее число if, чем CEventLog::Add', $stackPtr, 'AgentWantsDeeperIfSniff');
                }
            }

            foreach ($matches['ciblockelement'] as $master) {
                if ($slave['depth'] <= $master['depth']) {
                    $phpcsFile->addError('CEvent::Send должен быть вложен в большее число if, чем CIBlockElement::GetList', $stackPtr, 'AgentWantsDeeperIfSniff');
                }
            }
        }

//        if ($matches['cuser']['depth'] <= $matches['ceventlog']['depth']) {
//            $phpcsFile->addError('CUser::GetList должен быть вложен в большее число if, чем CEventLog::Add', $stackPtr, 'AgentWantsDeeperIfSniff');
//        }
//        if ($matches['cuser']['depth'] <= $matches['ciblockelement']['depth']) {
//            $phpcsFile->addError('CUser::GetList должен быть вложен в большее число if, чем CIBlockElement::GetList', $stackPtr, 'AgentWantsDeeperIfSniff');
//        }
//
//        if ($matches['cevent']['depth'] <= $matches['ceventlog']['depth']) {
//            $phpcsFile->addError('CEvent::Send должен быть вложен в большее число if, чем CEventLog::Add', $stackPtr, 'AgentWantsDeeperIfSniff');
//        }
//        if ($matches['cevent']['depth'] <= $matches['ciblockelement']['depth']) {
//            $phpcsFile->addError('CEvent::Send должен быть вложен в большее число if, чем CIBlockElement::GetList', $stackPtr, 'AgentWantsDeeperIfSniff');
//        }
    }
}
