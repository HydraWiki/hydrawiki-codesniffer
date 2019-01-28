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
		if ($preToken !== false &&
			$tokens[$preToken]['line'] === $tokens[$stackPtr]['line']
		) {
			$phpcsFile->addWarning(
				'Comments should start on new line.',
				$stackPtr,
				'NewLineComment'
			);
		}
		if ($currToken['code'] === T_COMMENT) {
			// Accounting for multiple line comments, as single line comments
			// use only '//' and '#'
			// Also ignoring PHPDoc comments starting with '///',
			// as there are no coding standards documented for these
			if (substr($currToken['content'], 0, 2) === '/*'
				|| substr($currToken['content'], 0, 3) === '///'
			) {
				return;
			}

			// Checking whether the comment is an empty one
			if ((substr($currToken['content'], 0, 2) === '//' &&
				rtrim($currToken['content']) === '//') ||
				($currToken['content'][0] === '#' &&
					rtrim($currToken['content']) === '#')
			) {
				return;
				// Checking whether there is a space between the comment delimiter
				// and the comment
			} elseif (substr($currToken['content'], 0, 2) === '//') {
				$commentContent = substr($currToken['content'], 2);
				$commentTrim = ltrim($commentContent);
				if (strlen($commentContent) == strlen($commentTrim)) {
					$error = 'At least a single space expected after "//"';
					$fix = $phpcsFile->addFixableWarning(
						$error,
						$stackPtr,
						'SingleSpaceBeforeSingleLineComment'
					);
					if ($fix) {
						$newContent = '// ';
						$newContent .= $commentTrim;
						$phpcsFile->fixer->replaceToken($stackPtr, $newContent);
					}
				}
				// Finding what the comment delimiter is and checking whether there is a space
				// between the comment delimiter and the comment.
			} elseif ($currToken['content'][0] === '#') {
				// Find number of `#` used.
				$startComment = 0;
				while ($currToken['content'][$startComment] === '#') {
					$startComment += 1;
				}
				if ($currToken['content'][$startComment] !== ' ') {
					$error = 'At least a single space expected after "#"';
					$fix = $phpcsFile->addFixableWarning(
						$error,
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
	}
}
