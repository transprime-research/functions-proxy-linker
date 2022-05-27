<?php declare(strict_types=1);

namespace Transprime\FunctionsLinker;

use Nette\PhpGenerator\ClassLike;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PsrPrinter;
use ReflectionFunction;

class Linker
{
    private ClassLike $class;
    private string $classFile;

    public function on(string $classFile, array $except = []): self
    {
        $this->classFile = $classFile;
        $this->class = ClassType::from($this->classFile, true);

        array_map(
            fn($functionType) => $this->getPHPFunctions([$this->class, 'addComment'], $functionType, $except),
            ['internal'],
        );

        return $this;
    }

    public function save(string $storagePath)
    {
        $classContent = (new PsrPrinter)->printClass($this->class);

        file_put_contents(
            $storagePath,
            "<?php\n\n namespace ".substr($this->classFile, 0, strrpos($this->classFile, '\\')).";\n\n"
            .$classContent
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function getPHPFunctions(callable $callOnEachMethod, string $functionType, array $except): void
    {
        $functions = get_defined_functions()[$functionType];

        foreach ($functions as $function) {
            if (in_array($function, $except, true)) {
                continue;
            }

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

            if (false === strripos($function, '\\')) {
                $callOnEachMethod('@method self ' . $function . '(' . implode(', ', $args) . ')');
            } else {
                $functionPaths = explode('\\', $function);
                $functionName = $functionPaths[count($functionPaths)-1];
                $callOnEachMethod('@method self ' . $functionName . '(' . implode(', ', $args) . ')'." <$function>");
            }
            unset($args);
        }
    }

    private function paramIsOptional(\ReflectionParameter $parameter): bool
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