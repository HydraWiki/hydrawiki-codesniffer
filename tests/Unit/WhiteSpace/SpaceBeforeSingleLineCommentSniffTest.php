<?php

namespace Tests\Unit\WhiteSpace;

use Test\BaseTest;
use HydraWiki\Sniffs\WhiteSpace\SpaceBeforeSingleLineCommentSniff;

class SpaceBeforeSingleLineCommentSniffTest extends BaseTest {
	protected $sniff;

	/**
	 * Setup sniff
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();

		$this->sniff = new SpaceBeforeSingleLineCommentSniff();
	}

	/**
	 * test sniff with correctly formatted comment starting with '//'
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessCorrectlyFormattedCommentSlahes() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 1
			],
			1 => [
				'content' => '// this is a correct comment.',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->exactly(0))
			->method('addWarning');

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableWarning');

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}

	/**
	 * Test sniff with correctly formatted comment starting with '#'
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessCorrectlyFormattedCommentHash() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 1
			],
			1 => [
				'content' => '# this is a correct comment.',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->exactly(0))
			->method('addWarning');

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableWarning');

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}

	/**
	 * Test sniff with comment at the end of a line of code
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessCommentNotOnNewLine() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 2
			],
			1 => [
				'content' => '// this is after a line of code.',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->once())
			->method('addWarning')
			->with(
				$this->stringContains('Comments should start on new line.'),
				$this->equalTo(1),
				$this->equalTo('NewLineComment')
			);

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableWarning');

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}

	/**
	 * Test sniff with incorrectly formatted comment starting with '//'
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessIncorrectlyFormattedCommentSlash() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 1
			],
			1 => [
				'content' => '//this is a correct comment.',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->exactly(0))
			->method('addWarning');

		// test that a fixable warning is created.
		$this->fileMock->expects($this->once())
			->method('addFixableWarning')
			->with(
				$this->stringContains('At least a single space expected after "//"'),
				$this->equalTo(1),
				$this->equalTo('SingleSpaceBeforeSingleLineComment')
			)
			->willReturn(true);

		// test that the coment is fixed.
		$this->fileMock->fixer->expects($this->once())
			->method('replaceToken')
			->with(
				$this->equalTo(1),
				$this->stringContains('// this is a correct comment.')
			);

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}

	/**
	 * Test sniff with incorrectly formatted comment starting with '#'
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessIncorrectlyFormattedCommentHash() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 1
			],
			1 => [
				'content' => '#this is a correct comment.',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->exactly(0))
			->method('addWarning');

		// test that a fixable warning is created.
		$this->fileMock->expects($this->once())
			->method('addFixableWarning')
			->with(
				$this->stringContains('At least a single space expected after "#"'),
				$this->equalTo(1),
				$this->equalTo('SingleSpaceBeforeSingleLineComment')
			)
			->willReturn(true);

		// test that the coment is fixed.
		$this->fileMock->fixer->expects($this->once())
			->method('replaceToken')
			->with(
				$this->equalTo(1),
				$this->stringContains('# this is a correct comment.')
			);

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}

	/**
	 * Test sniff comment from doc block
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessSkipsDocBlocks() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 1
			],
			1 => [
				'content' => '/*',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->exactly(0))
			->method('addWarning');

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableWarning');

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}

	/**
	 * Test sniff with an empty comment
	 * @covers SpaceBeforeSingleLineCommentSniff::process
	 *
	 * @return void
	 */
	public function testProcessSkipsEmptyCommentLines() {
		$token = [
			0 => [
				'content' => ' ',
				'code' => T_WHITESPACE,
				'line' => 1
			],
			1 => [
				'content' => '//',
				'code' => T_COMMENT,
				'line' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($token);
		$this->fileMock->method('findPrevious')
			->willReturn(0);

		$this->fileMock->expects($this->exactly(0))
			->method('addWarning');

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableWarning');

		$result = $this->sniff->process($this->fileMock, 1);

		$this->assertNull($result);
	}
}
