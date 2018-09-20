<?php

namespace Test\Unit\Commenting;

use Test\BaseTest;
use Composer\Spdx\SpdxLicenses;
use HydraWiki\Sniffs\Commenting\LicenseCommentSniff;

class LicenseCommentSniffTest extends BaseTest {
	protected $sniff;
	protected $spdxMock;

	/**
	 * Setup sniff
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();

		$this->sniff = new LicenseCommentSniff();

		$this->spdxMock = $this->createMock(SpdxLicenses::class);

		$this->sniff->initialize($this->fileMock, 0, $this->spdxMock);
	}

	/**
	 * test sniff when no whitespace is present
	 * @covers NoSpaceAfterNotSniff::process
	 * @return void
	 */
	public function testProcessWithInvalidLicense() {
		$tokens = [
			0 => [
				'content' => '/**',
				'code' => 'PHPCS_T_DOC_COMMENT_OPEN_TAG',
				'type' => 'T_DOC_COMMENT_OPEN_TAG',
				'comment_tags' => [6],
				'comment_closer' => 11,
				'line' => 2,
				'column' => 1,
				'length' => 3,
				'level' => 0,
				'conditions' => []
			],
			6 => [
				'content' => '@license',
				'code' => 'PHPCS_T_DOC_COMMENT_TAG',
				'type' => 'T_DOC_COMMENT_TAG',
				'line' => 3,
				'column' => 4,
				'length' => 8,
				'level' => 0,
				'conditions' => []
			],
			8 => [
				'content' => 'MIT2',
				'code' => 'PHPCS_T_DOC_COMMENT_STRING',
				'type' => 'T_DOC_COMMENT_STRING',
				'line' => 3,
				'column' => 14,
				'length' => 3,
				'level' => 0,
				'conditions' => []
			]
		];

		$this->fileMock->method('getTokens')
			 ->willReturn($tokens);

		$this->fileMock->method('findNext')
			 ->willReturn(8);

		$this->fileMock->expects($this->once())
			 ->method('addWarning')
			 ->with(
					$this->stringContains('Invalid SPDX license identifier "%s", see <https://spdx.org/licenses/>'),
					$this->equalTo(6),
					$this->equalTo('InvalidLicenseTag'),
					$this->identicalTo(['MIT2'])
				)
			 ->willReturn(false);
		$this->sniff->process($this->fileMock, 0);
	}
}
