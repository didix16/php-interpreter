PHP Interpreter
=

A simple interpreter made in PHP. It allows to parse an abstract language tokens and do whatever you want

## Content

* [What is an Interpreter](#what-is-a-token)
* [Installation](#installation)
* [Usage](#usage)
* [Check also](#check-also)


### What is an Interpreter

An interpreter is a piece of software which allows parse an array of tokens of any language and
give them a "significance". For example you could process a function, call a service, do something funny...

As an example, imagine we have an APILexer and APIParser, which understands a metalanguage to script some instructions
related with REST APIS:

```php

$script = "
GET https://some-awesome-api.com/endpoint?token=TOKEN
PIPETO $mySuperService
SAVEDB localhost:10000
";


$lexer = new APILexer($script);
$parser = new APIParser($lexer);

$tokens = $parser->parse();

// var_dump($tokens) should be:

array(12) {
  [0]=>
  object(Token)#4 (2) {
    ["value":protected]=>
    string(3) "GET"
    ["type":protected]=>
    int(2)
  }
  [1]=>
  object(Token)#2 (2) {
    ["value":protected]=>
    string(1) " "
    ["type":protected]=>
    int(3)
  }
  [2]=>
  object(Token)#5 (2) {
    ["value":protected]=>
    string(49) "https://some-awesome-api.com/endpoint?token=TOKEN"
    ["type":protected]=>
    int(4)
  }
  [3]=>
  object(Token)#6 (2) {
    ["value":protected]=>
    string(1) "
"
    ["type":protected]=>
    int(5)
  }
  [4]=>
  object(Token)#7 (2) {
    ["value":protected]=>
    string(6) "PIPETO"
    ["type":protected]=>
    int(2)
  }
  [5]=>
  object(Token)#8 (2) {
    ["value":protected]=>
    string(1) " "
    ["type":protected]=>
    int(3)
  }
  [6]=>
  object(Token)#9 (2) {
    ["value":protected]=>
    string(15) "$mySuperService"
    ["type":protected]=>
    int(4)
  }
  [7]=>
  object(Token)#10 (2) {
    ["value":protected]=>
    string(1) "
"
    ["type":protected]=>
    int(5)
  }
  [8]=>
  object(Token)#11 (2) {
    ["value":protected]=>
    string(6) "SAVEDB"
    ["type":protected]=>
    int(2)
  }
  [9]=>
  object(Token)#12 (2) {
    ["value":protected]=>
    string(1) " "
    ["type":protected]=>
    int(3)
  }
  [10]=>
  object(Token)#13 (2) {
    ["value":protected]=>
    string(15) "localhost:10000"
    ["type":protected]=>
    int(4)
  }
  [11]=>
  object(Token)#14 (2) {
    ["value":protected]=>
    string(5) "<EOF>"
    ["type":protected]=>
    int(1)
  }
}
```

With these tokens we can do some kind of magic and create an Interpreter which process them. 
For example, we can call it: APIInterpreter.

```php

public class APIInterpreter extends Interpreter {

    public function __construct(APIParser $parser)
    {
        parent::__construct($parser, null);

        $this
            // Allows make GET using an http client or whatever...
            ->loadFunction(new APIGetFunction(...))
            // Allows to interact with DDBB and store values
            ->loadFunction(new DBFunction(...))
            // ...whatever
    }

    protected function consume(): TokenInterface {

        $token = parent::consume();
        if(
            $token->getValue() !== Lexer::EOF_VALUE &&
            $token->getType() === YOUR_TYPE)
            //do something

        return $token;
    }

    /**
     * Execute this interpreter using the parsed tokens
     */
    public function run()
    {
        // All your tokens processing logic goes here
        $token = $this->lookahead();

        // do something with GET token and whitespace token, or whatever...

        do {
            //...
        }while(($token = $this->lookahead()));
    }

    // Write your auxiliar functions here

    /**
     * this one could parse each starting command line:
     * GET, PIPETO, SAVEDB, etc...
     */
    public function processCommand()
    {
        ...
    }

    /**
     * This one could resolve some service from PIPETO command...
     */
    public function processService()
    {
        ...
    }
}
```

Well that is a super simple example. You have to left your imagination work and do something special :)


### Installation

```php
composer require didix16/php-interpreter
```

### Usage

Using the example above, 
I'm gonna show a simple usage to instruct the APIInterpeter how it should be acting when reading our great APIScript:

A simple script would have a structure like this:

```
GET https://some-awesome-api.com/endpoint?token=TOKEN
PIPETO $mySuperService
SAVEDB localhost:10000
```

We can identify lines, which each line is an action.
Each action has a keyname, a space and a param ( it could be a list of params)

So lets say we have to identify tokens like:

T_ACTION, T_WHITESPACE, T_ARG and T_NEWLINE

```php
<?php

// APILexer.php

use didix16\Grammar\Lexer;
use didix16\Grammar\Token;
use didix16\Interpreter\Interpreter;

// Imported an APIParser
use ...\APIParser;

public class APIGetFunction extends InterpreterFunction {

    public function __construct()
    {
        parent::__construct("GET");
    }

    public function run(...$args)
    {
        /**
         * $url = $args[0];
         * return request()->get($url)
         * or whatever
         */
    }
}

public class DBFunction extends InterpreterFunction {

    public function __construct()
    {
        parent::__construct("SAVEDB");
    }

    public function run(...$args)
    {
        /**
         * Get the data to be stored to DDBB.
         * Preprocess the data if you need it
         * 
         * return DDBB::connection($args[0])->store($data)
         */
    }
}

public class APIInterpreter extends Interpreter {

    /**
     * Hold API response result, for example or result from executed function
     */
    private $currentResult;

    public function __construct(APIParser $parser)
    {
        parent::__construct($parser, null);

        $this
            // Allows make GET using an http client or whatever...
            ->loadFunction(new APIGetFunction(...))
            // Allows to interact with DDBB and store values
            ->loadFunction(new DBFunction(...))
            // ...whatever
    }

    /**
     * Execute this interpreter using the parsed tokens
     */
    public function run()
    {
        // All your tokens processing logic goes here
        $token = $this->lookahead();

        do {
            
            switch($token->getType()){
                case APILexer::T_ACTION:
                    $this->processCommand();
                    break;
                default:
                    // ignore whitespaces and newlines
                    // arguments should never be at start of a line
                    $this->consume();
            }
                

        }while(($token = $this->lookahead()));

        // Returns something meaningful to understand that your interpreter has been finished ok
        // Or return some data if needs to
    }

    // Write your auxiliar functions here

    /**
     * this one could parse each starting command line:
     * GET, PIPETO, SAVEDB, etc...
     */
    public function processCommand()
    {
        $command = $this->consume()->getValue();
        $args = [];

        
        while($this->lookahead()->getType() !== APILexer::T_NEWLINE)
        {
            if($this->lookahead()->getType() === APILexer::T_WHITESPACE)
                $this->consume(); // ' '
            else
                $args[] = $this->processCommandArg();
        }
        $this->consume(); // consume new line

        $this->currentResult = $this->executeFunction($command, $args);
    }

    public function processCommandArg()
    {
        $value = $this->consume()->getValue();

        // make some processing of the argument if you need...
        //transform($value);
        /**
         * if $value === $mySuperService then
         * import superService or whatever
         */

        return $value;
    }

    /**
     * This one could resolve some service from PIPETO command...
     */
    public function processService()
    {
        ...
        // call the imported service and do something with previous result
        // Note that services array must be defined
        $this->services[$mySuperService]->doSomething($this->currentResult);
    }
}
```

Now with the interpreter, you can take some actions in function of each command. You are free to add as more commands you need.

### Check also

* [php-grammar][1] - A simple library to make Lexer and Parsers to build a language.

[1]:https://github.com/didix16/php-grammar