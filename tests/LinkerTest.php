<?php

declare(strict_types=1);

namespace Transprime\FunctionsLinker\Tests;

use PHPUnit\Framework\TestCase;
use Transprime\FunctionsLinker\Linker;
use Transprime\FunctionsLinker\Link;

class LinkerTest extends TestCase
{
    private array $initial;

    protected function setUp(): void
    {
        parent::setUp();
        $path = __DIR__ . '/../src/Link.php';

        $this->initial[0] = $path;
        $this->initial[1] = file_get_contents($path);
    }

    public function testOn(): void
    {
        $this->assertStringNotContainsString('is_array', $this->initial[1]);

        $linker = new Linker();
        $linker->on(Link::class)
            ->save($this->initial[0]);

        $this->assertStringContainsString('is_array', file_get_contents($this->initial[0]));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        file_put_contents(...$this->initial);
    }
}