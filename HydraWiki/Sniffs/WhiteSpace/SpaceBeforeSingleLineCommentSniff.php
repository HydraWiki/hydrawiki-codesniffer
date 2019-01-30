<?php
/**
 * Curse Inc.
 * SpaceBeforeSingleLineCommentSniff
 *
 * Verify comments are preceeded by a single space.
 *
 * This file was copied from MediaWiki Codesniffer before being modified
 * File: MediaWiki/Sniffs/WhiteSpace/SpaceBeforeSingleLineCommentSniff.php
 * From repository: https://github.com/wikimedia/mediawiki-tools-codesniffer
 *
 * @package	HydraWiki
 * @author Dieser Benutzer
 * @author Samuel Hilson <shhilson@curse.com>
 * @copyright https://github.com/wikimedia/mediawiki-tools-codesniffer/blob/master/COPYRIGHT
 * @license https://github.com/wikimedia/mediawiki-tools-codesniffer/blob/master/LICENSE GPL-2.0-or-later
 */

namespace HydraWiki\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SpaceBeforeSingleLineCommentSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_COMMENT];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$currToken = $tokens[$stackPtr];
		$preToken = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		// Test if comment starts on a new line
		if ($preToken !== false &&
			$tokens[$preToken]['line'] === $tokens[$stackPtr]['line']
		) {
			$phpcsFile->addWarning(
				'Comments should start on new line.',
				$stackPtr,
				'NewLineComment'
			);
		}

		// Validate Current Token
		if (!$this->isComment($currToken) ||
			$this->isDocBlockComment($currToken) ||
			$this->isEmptyComment($currToken)
		) {
			return;
		}

		// Checking whether there is a space between the comment delimiter
		// and the comment
		if (substr($currToken['content'], 0, 2) === '//') {
			$this->handleDoubleSlashComment($phpcsFile, $stackPtr, $currToken);
			return;
		}

		// Finding what the comment delimiter is and checking whether there is a space
		// between the comment delimiter and the comment.
		if ($currToken['content'][0] === '#') {
			$this->handleHashComment($phpcsFile, $stackPtr, $currToken);
		}
	}

	/**
	 * Check contents of current token for empty state
	 *
	 * @param array $currToken
	 * @return boolean
	 */
	private function isEmptyComment($currToken) {
		return (
			(substr($currToken['content'], 0, 2) === '//' && rtrim($currToken['content']) === '//') ||
			($currToken['content'][0] === '#' && rtrim($currToken['content']) === '#')
		);
	}

	/**
	 * Check contents of current token for doc block
	 *
	 * @param array $currToken
	 * @return boolean
	 */
	private function isDocBlockComment($currToken) {
		// Accounting for multiple line comments, as single line comments
		// use only '//' and '#'
		// Also ignoring PHPDoc comments starting with '///',
		// as there are no coding standards documented for these
		return (substr($currToken['content'], 0, 2) === '/*' || substr($currToken['content'], 0, 3) === '///');
	}

	/**
	 * Check if current token is a comment token
	 *
	 * @param [type] $currToken
	 * @return boolean
	 */
	private function isComment($currToken) {
		return $currToken['code'] === T_COMMENT;
	}

	/**
	 * handle any double slash  style'//' comments
	 *
	 * @param File $phpcsFile
	 * @param int $stackPtr
	 * @param array $currToken
	 * @return void
	 */
	private function handleDoubleSlashComment($phpcsFile, $stackPtr, $currToken) {
		$commentContent = substr($currToken['content'], 2);
		$commentTrim = ltrim($commentContent);
		if (strlen($commentContent) == strlen($commentTrim)) {
			$fix = $phpcsFile->addFixableWarning(
				'At least a single space expected after "//"',
				$stackPtr,
				'SingleSpaceBeforeSingleLineComment'
			);
			if ($fix) {
				$newContent = '// ';
				$newContent .= $commentTrim;
				$phpcsFile->fixer->replaceToken($stackPtr, $newContent);
			}
		}
	}

	/**
	 * handle any hash style '#' comments
	 *
	 * @param File $phpcsFile
	 * @param int $stackPtr
	 * @param array $currToken
	 * @return void
	 */
	private function handleHashComment($phpcsFile, $stackPtr, $currToken) {
		// Find number of `#` used.
		$startComment = 0;
		while ($currToken['content'][$startComment] === '#') {
			$startComment += 1;
		}
		if ($currToken['content'][$startComment] !== ' ') {
			$fix = $phpcsFile->addFixableWarning(
				'At least a single space expected after "#"',
				$stackPtr,
				'SingleSpaceBeforeSingleLineComment'
			);
			if ($fix) {
				$content = $currToken['content'];
				$newContent = '# ';
				$tmpContent = substr($content, 1);
				$newContent .= ltrim($tmpContent);
				$phpcsFile->fixer->replaceToken($stackPtr, $newContent);
			}
		}
	}
}
