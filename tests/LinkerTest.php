<?php

declare(strict_types=1);

namespace Transprime\FunctionsLinker\Tests;

use Nette\PhpGenerator\ClassType;
use PHPUnit\Framework\TestCase;
use Transprime\FunctionsLinker\Linker;
use Transprime\FunctionsLinker\Tests\Stub\LinkerStub;

class LinkerTest extends TestCase
{
    private array $initial;

    public function testOn()
    {
        $path = __DIR__.'/stub/LinkerStub.php';

        $this->initial[0] = $path;
        $this->initial[1] = file_get_contents($path);

        $this->assertStringNotContainsString('is_array', $this->initial[1]);

        $linker = new Linker();

        $linker->on(LinkerStub::class)
            ->save(__DIR__.'/stub/LinkerStub.php');

        $this->assertStringContainsString('is_array', file_get_contents($this->initial[0]));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        file_put_contents(...$this->initial);
    }
}