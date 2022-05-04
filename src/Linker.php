<?php

declare(strict_types=1);

namespace Transprime\FunctionsLinker;

use ReflectionFunction;
use Transprime\FunctionsLinker\Tests\Stub\LinkerStub;

class Linker extends LinkerStub
{
    public function on(string $classFile, string $storagePath)
    {
        $class = \Nette\PhpGenerator\ClassType::from($classFile, true);

        $this->getPHPFunctions([$class, 'addComment']);

        file_put_contents(
            $storagePath,
            "<?php\n\n namespace ".substr($classFile, 0, strrpos( $classFile, '\\')).";\n\n"
            .(new \Nette\PhpGenerator\PsrPrinter)->printClass($class)
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function getPHPFunctions(callable $callOnEachMethod): void
    {
        $functions = get_defined_functions()['internal'];

        foreach ($functions as $function) {
            $reflectionFunction = new ReflectionFunction($function);

            // Skip functions with no parameters
            if (!$reflectionFunction->getNumberOfParameters()) {
                continue;
            }

            $args = [];
            foreach ($reflectionFunction->getParameters() as $param) {
                $temporaryArgument = $param->getType().' ';
                if ($param->isPassedByReference())  {
                    $temporaryArgument .= ' &';
                }
                if ($this->paramIsOptional($param)) {
                    $temporaryArgument .= '$' . $param->getName() . ' = ' . json_encode($param->getDefaultValue());
                } else {
                    $temporaryArgument.= '$' . $param->getName();
                }
                $args[] = $temporaryArgument;
                unset ($temporaryArgument);
            }
            $callOnEachMethod('@method self ' . $function . '(' . implode(', ', $args) . ')');
            unset($args);
        }
    }


    private function paramIsOptional(\ReflectionParameter $parameter)
    {
        try {
            $optional = $parameter->isOptional();
            $parameter->getDefaultValue();
        } catch (\ReflectionException $exception) {
            $optional = false;
        }

        return $optional;
    }
}