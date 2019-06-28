<?php

namespace Parameters;

class Result
{
    /**
     * Resulting value.
     *
     * @var mixed
     */
    private $value = null;

    /**
     * If set to true, the parser will be halted.
     *
     * @var bool
     */
    private $shouldHalt = false;

    /**
     * Construct the parameter result with a value
     * and a halt value.
     *
     * @param mixed $value
     * @param bool  $halt
     */
    public function __construct($value, $halt = false)
    {
        $this->value = $value;
        $this->shouldHalt = $halt;
    }

    /**
     * Retrieve the value of the Result.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Check if this Result should ONLY halt the parser.
     *
     * @return bool
     */
    public function isHaltOnly()
    {
        return $this->value == Parser::HALT_PARSE;
    }

    /**
     * Check if apart of the result includes halting
     * the parser.
     *
     * @return bool
     */
    public function shouldHalt()
    {
        return $this->shouldHalt;
    }
}
