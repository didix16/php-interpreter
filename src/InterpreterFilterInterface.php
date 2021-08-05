<?php


namespace didix16\Interpreter;

interface InterpreterFilterInterface
{
    /**
     * The constructor of an interpreter filter
     * It should be passed a name as a parameter.
     * The name should be stored using strtolower
     */
    public function __construct(string $name);

    /**
     * Returns the name of the filter
     */
    public function getName(): string;

    /**
     * The filter should be invoked as a function.
     * It must do something with a value, i.e: transform the value
     */
    public function __invoke(&$value);

}