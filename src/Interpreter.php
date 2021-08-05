<?php

namespace didix16\Interpreter;

use didix16\Grammar\ParserInterface;
use didix16\Grammar\TokenInterface;
use Exception;

/**
 * An interpreter that makes some actions applied to given data
 * Class Interpreter
 * @package didix16\Interpreter
 */
abstract class Interpreter
{

    /**
     * Function map that contains all loaded functions for this interpreter
     * @var InterpreterFunction[]
     */
    protected $functions = [];
    
    /**
     * @var TokenInterface[]
     */
    protected  $tokens = [];
    /**
     * The current token being processed
     * @var TokenInterface
     */
    protected $currentToken = null;
    /**
     * Last token that has been processed
     * @var TokenInterface|null
     */
    protected $lastToken = null;
    /**
     * @var mixed
     */
    protected $data;

    public function __construct(ParserInterface $parser, $data)
    {
        $this->lastToken = $this->lookahead();
        $this->tokens = $parser->parse();
        $this->data = $data;
    }

    /**
     * Execute this interpreter using the parsed tokens
     * @return mixed
     */
    abstract protected function run();

    /**
     * Consumes the current token
     * Returns null if there are not more tokens
     * @return TokenInterface
     */
    protected function consume(): TokenInterface {

        $this->lastToken = $this->currentToken;
        $this->currentToken = array_shift($this->tokens);
        return $this->currentToken;
    }

    /**
     * Returns the next token in token list.
     * If there are not more tokens then should return null
     * @return Token
     */
    protected function lookahead(): ?TokenInterface {

        $token = current($this->tokens);
        return  $token ? $token : null;
    }

    /**
     * Returns the last consumed token. If no token was consumed then behind == ahead
     * @return Token
     */
    protected function lookbehind(): TokenInterface {

        return $this->lastToken;
    }

    /**
     * Loads a function for this interpreter
     * @param InterpreterFunction $fn
     * @return $this
     */
    protected function loadFunction(InterpreterFunction $fn){

        $this->functions[strtolower($fn->getName())] = $fn;
        return $this;
    }

    /**
     * Unloads a loaded function from this interpreter
     * @param $fnName
     * @return Interpreter
     * @throws Exception
     */
    final protected function unloadFunction($fnName){

        $fnName = strtolower($fnName);

        if ($this->functionExists($fnName)) {
            unset($this->functions[$fnName]);
        } else {
            throw new Exception("Function $fnName could not be unloaded because it is not loaded.");
        }

        return $this;
    }

    /**
     * Given a function name, check if is loaded into the interpreter
     * @param $fnName
     * @return bool
     */
    private function functionExists($fnName){

        return isset($this->functions[$fnName]);
    }

    /**
     * Executes the given function name if it is loaded
     * @param $fnName
     * @param mixed ...$args
     * @return mixed
     * @throws Exception
     */
    final protected function executeFunction($fnName, ...$args){

        $fnName = strtolower($fnName);
        if ($this->functionExists($fnName)){
            return $this->functions[$fnName](...$args);
        }else{
            throw new Exception("Function $fnName does not exists. Maybe it is not loaded?");
        }
    }

    
}