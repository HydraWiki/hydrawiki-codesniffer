<?php
/**
 * Curse Inc.
 * LicenseCommentSniff
 *
 * This file was copied from MediaWiki Codesniffer before being modified
 * File: MediaWiki/Sniffs/Commenting/LicenseCommentSniff.php
 * From repository: https://github.com/wikimedia/mediawiki-tools-codesniffer
 *
 * @package	HydraWiki
 * @author Dieser Benutzer
 * @author Samuel Hilson <shhilson@curse.com>
 * @copyright https://github.com/wikimedia/mediawiki-tools-codesniffer/blob/master/COPYRIGHT
 * @license https://github.com/wikimedia/mediawiki-tools-codesniffer/blob/master/LICENSE GPL-2.0-or-later
 */
namespace HydraWiki\Sniffs\Commenting;

use Composer\Spdx\SpdxLicenses;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class LicenseCommentSniff implements Sniff {

	private $file;
	private $tokens;
	private $end;

	/** @var SpdxLicenses */
	private $spdx = null;

	/**
	 * Common auto-fixable replacements
	 *
	 * @var array regex -> replacement
	 */
	private $replacements = [
		'GNU General Public Licen[sc]e 2(\.0)? or later' => 'GPL-2.0-or-later',
		'GNU GPL v2\+' => 'GPL-2.0-or-later',
		'All Rights Reserved(\.)?' => 'Proprietary'
	];

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [ T_DOC_COMMENT_OPEN_TAG ];
	}

	/**
	 * Initialize
	 *
	 * @param File $phpcsFile
	 * @param int $stackPtr
	 * @param SpdxLicenses|null $spdx
	 * @return void
	 */
	public function initialize(File $phpcsFile, $stackPtr, SpdxLicenses $spdx = null) {
		$this->file = $phpcsFile;
		$this->tokens = $this->file->getTokens();
		$this->end = $this->tokens[$stackPtr]['comment_closer'];
		if ($spdx !== null) {
			$this->spdx = $spdx;
		}
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in the stack passed in $tokens.
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$this->initialize($phpcsFile, $stackPtr);

		foreach ($this->tokens[$stackPtr]['comment_tags'] as $tag) {
			$this->processDocTag($tag);
		}
	}

	/**
	 * handle a single doc line
	 *
	 * @param string $tag
	 * @return mixed
	 */
	private function processDocTag($tag) {
		if ($this->isCorrectTag($tag)) {
			return;
		}

		// Only allow license on file comments
		$this->isValidTagLocation($tag);

		// Validate text behind license
		$next = $this->file->findNext([T_DOC_COMMENT_WHITESPACE], $tag + 1, $this->end, true);

		if ($this->hasTextAfterTag($tag, $next)) {
			return;
		}

		// Get license text
		$license = $this->getLicenseFromTag($next);

		// Allow for proprietary licensing to be used
		if ($this->isValidProprietaryLicense($license)) {
			return;
		}

		// Flag deprecated licenses
		if ($this->isLicenseDeprecated($tag, $license)) {
			return;
		}

		// Validate licenses
		$this->handleInvalidLicense($tag, $next, $license);
	}

	/**
	 * get or initialize SpdxLicenses library
	 *
	 * @return SpdxLicenses
	 */
	private function getLicenseValidator() {
		if ($this->spdx === null) {
			$this->spdx = new SpdxLicenses();
		}
		return $this->spdx;
	}

	/**
	 * normalize license text
	 *
	 * @param string $next
	 * @return string
	 */
	private function getLicenseFromTag($next) {
		$license = $this->tokens[$next]['content'];

		// license can contain a url, use the text behind it
		if (preg_match('/^https?:\/\/[^\s]+\s+(.*)/', $license, $match)) {
			$license = $match[1];
		}
		return $license;
	}

	/**
	 * check if we are on the correct license tag
	 *
	 * @param string $tag
	 * @return boolean
	 */
	private function isCorrectTag($tag) {
		$tagText = $this->tokens[$tag]['content'];
		switch ($tagText) {
			case '@licence':
				$fix = $this->file->addFixableWarning(
					'Incorrect spelling of @license',
					$tag,
					'LicenceTag'
				);
				if ($fix) {
					$this->file->fixer->replaceToken($tag, '@license');
				}
				break;
			case '@license':
				break;
			default:
				return true;
		}

		return false;
	}

	/**
	 * check the tag location is in file level doc comment
	 *
	 * @param string $tag
	 * @return boolean
	 */
	private function isValidTagLocation($tag) {
		if ($this->tokens[$tag]['level'] !== 0) {
			$this->file->addWarning(
				'@license should only be used on file comments',
				$tag,
				'LicenseTagNonFileComment'
			);
		}
	}

	/**
	 * check that tag has some text after @license
	 *
	 * @param string $tag
	 * @param string $next
	 * @return boolean
	 */
	private function hasTextAfterTag($tag, $next) {
		if ($this->tokens[$next]['code'] !== T_DOC_COMMENT_STRING) {
			$this->file->addWarning(
				'@license not followed by a license',
				$tag,
				'LicenseTagEmpty'
			);
			return true;
		}
	}

	/**
	 * check for a private license
	 *
	 * @param string $license
	 * @return boolean
	 */
	private function isValidProprietaryLicense($license) {
		if (strtolower($license) == 'proprietary') {
			 return true;
		}
	}

	/**
	 * check if the license is marked as deprecated
	 *
	 * @param string $tag
	 * @param string $license
	 * @return boolean
	 */
	private function isLicenseDeprecated($tag, $license) {
		// Initialize the spdx license validator
		$spdx = $this->getLicenseValidator();

		$valid = $spdx->validate($license);
		// Check for Deprecated licensing
		if ($valid && $spdx->isDeprecatedByIdentifier($license)) {
			$this->file->addWarning(
				'Deprecated SPDX license identifier "%s", see <https://spdx.org/licenses/>',
				$tag,
				'DeprecatedLicenseTag',
				[$license]
			);
			return true;
		}
	}

	/**
	 * checks that the license is a valid license from spdx
	 *
	 * @param string $tag
	 * @param string $next
	 * @param string $license
	 * @return boolean
	 */
	private function handleInvalidLicense($tag, $next, $license) {
		// Initialize the spdx license validator
		$spdx = $this->getLicenseValidator();

		$valid = $spdx->validate($license);

		if ($valid) {
			return;
		}

		$fixable = null;
		foreach ($this->replacements as $regex => $identifier) {
			// Make sure the entire license matches the regex, and
			// then a sanity check that the new replacement is valid too
			if (preg_match("/^$regex$/", $license) === 1
				&& ($spdx->validate($identifier) || $this->isValidProprietaryLicense($identifier))
			) {
				$fixable = $identifier;
				break;
			}
		}

		// handle fixable license problem
		if ($fixable !== null) {
			$fix = $this->file->addFixableWarning(
				'Invalid SPDX license identifier "%s", see <https://spdx.org/licenses/>',
				$tag,
				'InvalidLicenseTag',
				[$license]
			);
			if ($fix) {
				$this->file->fixer->replaceToken($next, $fixable);
			}
			return;
		}

		// report un-fixable problems
		$this->file->addWarning(
			'Invalid SPDX license identifier "%s", see <https://spdx.org/licenses/>',
			$tag,
			'InvalidLicenseTag',
			[$license]
		);
	}
}
