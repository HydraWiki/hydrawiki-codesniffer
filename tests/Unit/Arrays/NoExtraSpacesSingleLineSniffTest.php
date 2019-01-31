<?php

namespace Tests\Unit\Arrays;

use Test\BaseTest;
use HydraWiki\Sniffs\Arrays\NoExtraSpacesSingleLineSniff;

class NoExtraSpacesSingleLineSniffTest extends BaseTest {
	/**
	 * Container for the sniff under test
	 *
	 * @var NoExtraSpacingSingleLineSniff
	 */
	protected $sniff;

	/**
	 * Setup sniff
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();

		$this->sniff = new NoExtraSpacesSingleLineSniff();
	}

	/**
	 * Test Sniff with a good array
	 *
	 * @covers NoSpaceAfterNotSniff::process
	 *
	 * @return void
	 */
	public function testCorrectlyFormattedArray() {
		$tokens = [
			0 => [
				'type' => 'T_OPEN_SHORT_ARRAY',
				'code' => 'PHPCS_T_OPEN_SHORT_ARRAY',
				'contents' => '[',
				'line' => 1,
				'column' => 1,
				'length' => 1,
				'bracket_opener' => 0,
				'bracket_closer' => 6,
				'level' => 2
			],
			1 => [
				'type' => 'T_CONSTANT_ENCAPSED_STRING',
				'code' => 323,
				'content' => "'here'",
				'line' => 1,
				'column' => 2,
				'length' => 6,
				'level' => 2
			],
			2 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 8,
				'length' => 1,
				'level' => 2
			],
			3 => [
				'type' => 'T_DOUBLE_ARROW',
				'code' => 268,
				'content' => '=>',
				'line' => 1,
				'column' => 9,
				'length' => 2,
				'level' => 2
			],
			4 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 11,
				'length' => 1,
				'level' => 2
			],
			5 => [
				'type' => 'T_CONSTANT_ENCAPSED_STRING',
				'code' => 323,
				'content' => "'for you'",
				'line' => 1,
				'column' => 12,
				'length' => 9,
				'level' => 2
			],
			6 => [
				'type' => 'T_CLOSE_SHORT_ARRAY',
				'code' => 'PHPCS_T_CLOSE_SHORT_ARRAY',
				'contents' => ']',
				'line' => 1,
				'column' => 21,
				'length' => 1,
				'bracket_opener' => 0,
				'bracket_closer' => 6,
				'level' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($tokens);

		$this->fileMock->method('findPrevious')
			->willReturn(5);

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableError');

		$this->sniff->process($this->fileMock, 0);
	}

	/**
	 * Test Sniff with a bad array
	 *
	 * @covers NoSpaceAfterNotSniff::process
	 *
	 * @return void
	 */
	public function testIncorrectlyFormattedArraySpaceAfterOpen() {
		$tokens = [
			0 => [
				'type' => 'T_OPEN_SHORT_ARRAY',
				'code' => 'PHPCS_T_OPEN_SHORT_ARRAY',
				'contents' => '[',
				'line' => 1,
				'column' => 1,
				'length' => 1,
				'bracket_opener' => 0,
				'bracket_closer' => 7,
				'level' => 2
			],
			1 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 2,
				'length' => 1,
				'level' => 2
			],
			2 => [
				'type' => 'T_CONSTANT_ENCAPSED_STRING',
				'code' => 323,
				'content' => "'here'",
				'line' => 1,
				'column' => 3,
				'length' => 6,
				'level' => 2
			],
			3 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 9,
				'length' => 1,
				'level' => 2
			],
			4 => [
				'type' => 'T_DOUBLE_ARROW',
				'code' => 268,
				'content' => '=>',
				'line' => 1,
				'column' => 10,
				'length' => 2,
				'level' => 2
			],
			5 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 12,
				'length' => 1,
				'level' => 2
			],
			6 => [
				'type' => 'T_CONSTANT_ENCAPSED_STRING',
				'code' => 323,
				'content' => "'for you'",
				'line' => 1,
				'column' => 13,
				'length' => 9,
				'level' => 2
			],
			7 => [
				'type' => 'T_CLOSE_SHORT_ARRAY',
				'code' => 'PHPCS_T_CLOSE_SHORT_ARRAY',
				'contents' => ']',
				'line' => 1,
				'column' => 22,
				'length' => 1,
				'bracket_opener' => 0,
				'bracket_closer' => 7,
				'level' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($tokens);

		$this->fileMock->method('findPrevious')
			->willReturn(6);

		$msg = 'No Spaces allowed after the opening brace of a single-line short array; %s found';
		$this->fileMock->expects($this->once())
			->method('addFixableError')
			->with(
				$this->stringContains($msg),
				$this->equalTo(0),
				$this->equalTo('SpaceAfterOpeningBrace'),
				$this->identicalTo([1])
			)
			->willReturn(false);

		$this->sniff->process($this->fileMock, 0);
	}

	/**
	 * Test Sniff with a bad array
	 *
	 * @covers NoSpaceAfterNotSniff::process
	 *
	 * @return void
	 */
	public function testIncorrectlyFormattedArraySpaceBeforeClose() {
		$tokens = [
			0 => [
				'type' => 'T_OPEN_SHORT_ARRAY',
				'code' => 'PHPCS_T_OPEN_SHORT_ARRAY',
				'contents' => '[',
				'line' => 1,
				'column' => 1,
				'length' => 1,
				'bracket_opener' => 0,
				'bracket_closer' => 7,
				'level' => 2
			],
			1 => [
				'type' => 'T_CONSTANT_ENCAPSED_STRING',
				'code' => 323,
				'content' => "'here'",
				'line' => 1,
				'column' => 2,
				'length' => 6,
				'level' => 2
			],
			2 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 8,
				'length' => 1,
				'level' => 2
			],
			3 => [
				'type' => 'T_DOUBLE_ARROW',
				'code' => 268,
				'content' => '=>',
				'line' => 1,
				'column' => 9,
				'length' => 2,
				'level' => 2
			],
			4 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 11,
				'length' => 1,
				'level' => 2
			],
			5 => [
				'type' => 'T_CONSTANT_ENCAPSED_STRING',
				'code' => 323,
				'content' => "'for you'",
				'line' => 1,
				'column' => 12,
				'length' => 9,
				'level' => 2
			],
			6 => [
				'type' => 'T_WHITESPACE',
				'code' => 382,
				'content' => ' ',
				'line' => 1,
				'column' => 21,
				'length' => 1,
				'level' => 2
			],
			7 => [
				'type' => 'T_CLOSE_SHORT_ARRAY',
				'code' => 'PHPCS_T_CLOSE_SHORT_ARRAY',
				'contents' => ']',
				'line' => 1,
				'column' => 22,
				'length' => 1,
				'bracket_opener' => 0,
				'bracket_closer' => 7,
				'level' => 2
			]
		];

		$this->fileMock->method('getTokens')
			->willReturn($tokens);

		$this->fileMock->method('findPrevious')
			->willReturn(6);

		$msg = 'No Spaces allowed before the closing brace of a single-line short array; %s found';
		$this->fileMock->expects($this->once())
			->method('addFixableError')
			->with(
				$this->stringContains($msg),
				$this->equalTo(7),
				$this->equalTo('SpaceBeforeClosingBrace'),
				$this->identicalTo([1])
			)
			->willReturn(false);

		$this->sniff->process($this->fileMock, 0);
	}
}
