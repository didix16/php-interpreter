<?php


namespace didix16\Interpreter;

interface InterpreterFilterInterface
{

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