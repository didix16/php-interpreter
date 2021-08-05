<?php


namespace didix16\Interpreter;


abstract class InterpreterFunction
{

    /**
     * @var string
     */
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of this function
     * @return string
     */
    public function getName(): string {

        return $this->name;
    }

    /**
     * Code to run when called
     * @param $args
     * @return mixed
     */
    protected abstract function run(...$args);

    /**
     * Execute code on this();
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return $this->run(...$args);
    }
}