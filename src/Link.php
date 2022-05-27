<?php

namespace Transprime\FunctionsLinker;

trait Link
{
    public function __call($name, $arguments)
    {
       $name(...$arguments);

       return $this;
    }
}
