<?php

namespace Test;

use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Files\File;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase {

	protected $fileMock;

	protected function setUp() {
		$this->fileMock = $this->createMock(File::class);
		$fixer = $this->createMock(Fixer::class);
		$this->fileMock->fixer = $fixer;
	}
}
