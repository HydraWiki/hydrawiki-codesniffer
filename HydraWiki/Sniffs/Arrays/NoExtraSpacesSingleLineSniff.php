<?php
/**
 * Curse Inc.
 * NoExtraSpacesSniff
 *
 * This file was copied from PHP_CodeSniffer before being modified
 * File: Standards/Generic/Sniffs/Arrays/ArrayIndentSniff.php
 * From repository: https://github.com/squizlabs/PHP_CodeSniffer
 *
 * Ensures there is no extra space in single line arrays.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD-3-Clause
 * @package   HydraWiki
 */

namespace HydraWiki\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractArraySniff;

class NoExtraSpacesSingleLineSniff extends AbstractArraySniff {
	/**
	 * The number of spaces each array key should be indented.
	 *
	 * @var integer
	 */
	public $indent = 4;

	/**
	 * Processes a single-line array definition.
	 *
	 * @param File    $phpcsFile  The current file being checked.
	 * @param integer $stackPtr   The position of the current token in the stack passed in $tokens.
	 * @param integer $arrayStart The token that starts the array definition.
	 * @param integer $arrayEnd   The token that ends the array definition.
	 * @param array   $indices    An array of token positions for the array keys, double arrows, and values.
	 *
	 * @return void
	 */
	public function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) {
		$this->handleArrayStart($phpcsFile, $arrayStart);
		$this->handleArrayEnd($phpcsFile, $arrayEnd);
	}

	/**
	 * Processes a multi-line array definition.
	 *
	 * @param File    $phpcsFile  The current file being checked.
	 * @param integer $stackPtr   The position of the current token in the stack passed in $tokens.
	 * @param integer $arrayStart The token that starts the array definition.
	 * @param integer $arrayEnd   The token that ends the array definition.
	 * @param array   $indices    An array of token positions for the array keys, double arrows, and values.
	 *
	 * @return void
	 */
	public function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices) {
		// No multiline processing.
	}

	/**
	 * Process the starting brace of a short array
	 *
	 * @param File    $phpcsFile
	 * @param integer $arrayStart
	 *
	 * @return void
	 */
	private function handleArrayStart($phpcsFile, $arrayStart) {
		$tokens = $phpcsFile->getTokens();
		$spacing = 0;
		if ($tokens[($arrayStart + 1)]['code'] === T_WHITESPACE) {
			$spacing = $tokens[($arrayStart + 1)]['length'];
		}
		if ($spacing == 0) {
			return;
		}
		$message = 'No Spaces allowed after the opening brace of a single-line short array; %s found';
		$fix     = $phpcsFile->addFixableError($message, $arrayStart, 'SpaceAfterOpeningBrace', [$spacing]);
		if ($fix === true) {
			$phpcsFile->fixer->replaceToken(($arrayStart + 1), '');
		}
	}

	/**
	 * Process the closing brace of a short array
	 *
	 * @param File    $phpcsFile
	 * @param integer $arrayEnd
	 *
	 * @return void
	 */
	private function handleArrayEnd($phpcsFile, $arrayEnd) {
		$tokens = $phpcsFile->getTokens();
		$spacing = 0;
		if ($tokens[($arrayEnd - 1)]['code'] === T_WHITESPACE) {
			$spacing = $tokens[($arrayEnd - 1)]['length'];
		}
		if ($spacing == 0) {
			return;
		}
		$message = 'No Spaces allowed before the closing brace of a single-line short array; %s found';
		$fix     = $phpcsFile->addFixableError($message, $arrayEnd, 'SpaceBeforeClosingBrace', [$spacing]);
		if ($fix === true) {
			$phpcsFile->fixer->replaceToken(($arrayEnd - 1), '');
		}
	}
}
