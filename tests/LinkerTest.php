<?php

declare(strict_types=1);

namespace Transprime\FunctionsLinker\Tests;

use PHPUnit\Framework\TestCase;
use Transprime\FunctionsLinker\Linker;
use Transprime\FunctionsLinker\Tests\Stub\LinkerStub;

class LinkerTest extends TestCase
{

    public function testOn()
    {
        $linker = new Linker();

        $linker->on(LinkerStub::class, __DIR__.'/stub/LinkerStub.php');
    }
}