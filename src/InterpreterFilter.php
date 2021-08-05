<?php

use didix16\DataTransformer\DataTransformer;
use didix16\Interpreter\InterpreterFilterInterface;

abstract class InterpreterFilter extends DataTransformer implements InterpreterFilterInterface {

    /**
     * @var string
     */
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}