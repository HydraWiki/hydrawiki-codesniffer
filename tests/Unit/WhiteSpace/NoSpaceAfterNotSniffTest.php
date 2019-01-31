<?php

namespace Tests\Unit\WhiteSpace;

use Test\BaseTest;
use HydraWiki\Sniffs\WhiteSpace\NoSpaceAfterNotSniff;

class NoSpaceAfterNotSniffTest extends BaseTest {
	/**
	 * Container for sniff under test
	 *
	 * @var NoSpaceAfterNotSniff
	 */
	protected $sniff;

	/**
	 * Setup sniff
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();

		$this->sniff = new NoSpaceAfterNotSniff();
	}

	/**
	 * Test sniff when no whitespace is present
	 *
	 * @covers NoSpaceAfterNotSniff::process
	 * @return void
	 */
	public function testProcessWithNoWhitespace() {
		$this->fileMock->method('getTokens')
			->willReturn(['', ['code' => '', 'length' => 0]]);

		$this->fileMock->expects($this->exactly(0))
			->method('addFixableError');

		$result = $this->sniff->process($this->fileMock, 0);

		$this->assertNull($result);
	}

	/**
	 * Test sniff when whitespace is present
	 *
	 * @covers NoSpaceAfterNotSniff::process
	 * @return void
	 */
	public function testProcessWithOneWhitespace() {
		$this->fileMock->method('getTokens')
			->willReturn(['', ['code' => T_WHITESPACE, 'length' => 1]]);

		$this->fileMock->expects($this->once())
			->method('addFixableError')
			->with(
				$this->stringContains('There must not be a space after a NOT operator; %s found'),
				$this->equalTo(0),
				$this->equalTo('SpaceAfterNot'),
				$this->identicalTo([1])
			)
			->willReturn(false);

		$this->sniff->process($this->fileMock, 0);
	}

	/**
	 * Test sniff when whitespace is present
	 *
	 * @covers NoSpaceAfterNotSniff::process
	 * @return void
	 */
	public function testProcessWithOneWhitespaceFixer() {
		$this->fileMock->method('getTokens')
			->willReturn(['', ['code' => T_WHITESPACE, 'length' => 1]]);

		$this->fileMock->expects($this->once())
			->method('addFixableError')
			->willReturn(true);

		$this->fileMock->fixer->expects($this->once())
			->method('replaceToken');

		$this->sniff->process($this->fileMock, 0);
	}
}
