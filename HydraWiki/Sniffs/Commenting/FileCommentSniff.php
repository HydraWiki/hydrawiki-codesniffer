<?php
/**
 * Curse Inc.
 * FileCommentSniff
 *
 * This file was copied from PHP_CodeSniffer before being modified
 * File: Standards/Squiz/Sniffs/Commenting/FileCommentSniff.php
 * From repository: https://github.com/squizlabs/PHP_CodeSniffer
 *
 * Parses and verifies the file doc comment.
 *
 * @author    Samuel Hilson <shhilson@curse.com>
 * @author    Squiz Pty Ltd <products@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD-3-Clause
 * @package   HydraWiki
 */

namespace HydraWiki\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class FileCommentSniff implements Sniff {
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
	 * Container for phpcsFile
	 *
	 * @var File
	 */
	private $file;

	/**
	 * Container for tokens array
	 *
	 * @var array
	 */
	private $tokens;

	/**
	 * Pointer for comment start
	 *
	 * @var integer
	 */
	private $start;

	/**
	 * Pointer for comment end
	 *
	 * @var integer
	 */
	private $end;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_OPEN_TAG];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param integer                     $stackPtr  The position of the current token
	 *                                               in the stack passed in $this->tokens.
	 *
	 * @return integer
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$this->file   = $phpcsFile;
		$this->tokens = $this->file->getTokens();
		$this->start  = $this->file->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

		if ($this->tokens[$this->start]['code'] === T_COMMENT) {
			$this->file->addError('You must use "/**" style comments for a file comment', $this->start, 'WrongStyle');
			$this->file->recordMetric($stackPtr, 'File has doc comment', 'yes');
			return ($this->file->numTokens + 1);
		} elseif ($this->start === false || $this->tokens[$this->start]['code'] !== T_DOC_COMMENT_OPEN_TAG) {
			$this->file->addError('Missing file doc comment', $stackPtr, 'Missing');
			$this->file->recordMetric($stackPtr, 'File has doc comment', 'no');
			return ($this->file->numTokens + 1);
		}

		if (isset($this->tokens[$this->start]['comment_closer']) === false
			|| ($this->tokens[$this->tokens[$this->start]['comment_closer']]['content'] === ''
			&& $this->tokens[$this->start]['comment_closer'] === ($this->file->numTokens - 1))
		) {
			// Don't process an unfinished file comment during live coding.
			return ($this->file->numTokens + 1);
		}

		$this->end = $this->tokens[$this->start]['comment_closer'];

		$nextToken = $this->file->findNext(
			T_WHITESPACE,
			($this->end + 1),
			null,
			true
		);

		$ignore = [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
			T_FUNCTION,
			T_CLOSURE,
			T_PUBLIC,
			T_PRIVATE,
			T_PROTECTED,
			T_FINAL,
			T_STATIC,
			T_ABSTRACT,
			T_CONST,
			T_PROPERTY,
			T_INCLUDE,
			T_INCLUDE_ONCE,
			T_REQUIRE,
			T_REQUIRE_ONCE,
		];

		if (in_array($this->tokens[$nextToken]['code'], $ignore) === true) {
			$this->file->addError('Missing file doc comment', $stackPtr, 'Missing');
			$this->file->recordMetric($stackPtr, 'File has doc comment', 'no');
			return ($this->file->numTokens + 1);
		}

		$this->file->recordMetric($stackPtr, 'File has doc comment', 'yes');

		// No blank line between the open tag and the file comment.
		if ($this->tokens[$this->start]['line'] > ($this->tokens[$stackPtr]['line'] + 1)) {
			$error = 'There must be no blank lines before the file comment';
			$this->file->addError($error, $stackPtr, 'SpacingAfterOpen');
		}

		// Exactly one blank line after the file comment.
		$next = $this->file->findNext(T_WHITESPACE, ($this->end + 1), null, true);
		if ($this->tokens[$next]['line'] !== ($this->tokens[$this->end]['line'] + 2)) {
			$error = 'There must be exactly one blank line after the file comment';
			$this->file->addError($error, $this->end, 'SpacingAfterComment');
		}

		// Required tags in correct order.
		$required = [
			'@author'     => true,
			'@copyright'  => true,
			'@license'    => true,
			'@package'    => true,
			'@link'       => false
		];

		// Check the comment format for problems
		$foundTags = [];
		foreach ($this->tokens[$this->start]['comment_tags'] as $tag) {
			$name       = $this->tokens[$tag]['content'];
			$isRequired = isset($required[$name]);

			$foundTags[] = $name;

			if ($isRequired === false) {
				continue;
			}

			$string = $this->file->findNext(T_DOC_COMMENT_STRING, $tag, $this->end);
			if ($string === false || $this->tokens[$string]['line'] !== $this->tokens[$tag]['line']) {
				$error = 'Content missing for %s tag in file comment';
				$data  = [$name];
				$this->file->addError($error, $tag, 'Empty' . ucfirst(substr($name, 1)) . 'Tag', $data);
				continue;
			}
		}

		$pos = 0;
		// Handle multiple author tags first
		if (($key = array_search('@author', $required)) !== false) {
			unset($required[$key]);
			foreach ($foundTags as $tag) {
				if ($tag != '@author') {
					if ($pos == 0) {
						$this->tagLinePosition($pos, $foundTags, '@author', true);
						$pos++;
					}
					break;
				}

				$this->tagLinePosition($pos, $foundTags, '@author', true);
				$pos++;
			}
		}
		// Check if the tags are in the correct position.
		foreach ($required as $tag => $require) {
			$this->tagLinePosition($pos, $foundTags, $tag, $require);
			$pos++;
		}

		// Ignore the rest of the file.
		return ($this->file->numTokens + 1);
	}

	/**
	 * Check the tag position and required status
	 *
	 * @param integer $pos
	 * @param array   $tags
	 * @param string  $tag
	 * @param boolean $require
	 *
	 * @return void
	 */
	private function tagLinePosition($pos, $tags, $tag, $require) {
		if (in_array($tag, $tags) === false && $require) {
			$error = 'Missing %s tag in file comment';
			$data  = [$tag];
			$this->file->addError($error, $this->end, 'Missing' . ucfirst(substr($tag, 1)) . 'Tag', $data);
		}

		if (isset($tags[$pos]) === false) {
			return;
		}

		if ($tags[$pos] !== $tag) {
			$error = 'The tag in position %s should be the %s tag';
			$data  = [
				($pos + 1),
				$tag,
			];
			$this->file->addError(
				$error,
				$this->tokens[$this->start]['comment_tags'][$pos],
				ucfirst(substr($tag, 1)) . 'TagOrder',
				$data
			);
		}
	}
}
