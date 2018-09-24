<?php
/**
 * Curse Inc.
 * NoSpaceAfterNotSniff
 *
 * @author		Samuel Hilson
 * @license		MIT
 * @package		HydraWiki
 * @link		http://www.curse.com/
 *
 **/
namespace HydraWiki\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoSpaceAfterNotSniff implements Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = [
		'PHP',
		'JS',
	];

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_BOOLEAN_NOT];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in
	 *                        the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$spacing = 0;
		if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
			$spacing = $tokens[($stackPtr + 1)]['length'];
		}
		if ($spacing == 0) {
			return;
		}
		$message = 'There must not be a space after a NOT operator; %s found';
		$fix     = $phpcsFile->addFixableError($message, $stackPtr, 'Incorrect', [$spacing]);
		if ($fix === true) {
			$phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
		}
	}
}
