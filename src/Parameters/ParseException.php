<?php

namespace Parameters;

use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class ParseException extends \Exception {

    /**
     * Error Codes
     */
    const INVALID_ARGUMENT_COUNT_ALIAS = 60001;
    const INVALID_ARGUMENT_COUNT_PARAMETER = 60002;
    const INVALID_ARGUMENT_COUNT_VARIADIC_ALIAS = 60003;
    const INVALID_ARGUMENT_COUNT_VARIADIC_PARAMETER = 60004;
    const MISSING_REQUIRED_ARGUMENT = 60005;

    /**
     * The parameter that caused this Parse exception.
     * 
     * @var \Parameter
     */
    private $parameter;

    /**
     * Create the ParseException.
     */
    public function __construct(?Parameter $parameter, $message, $code = 0, \Exception $previous = null)
    {
        $this->parameter = $parameter;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Convert this Exception to an ANSI String.
     * 
     * @return string
     */
    public function toAnsiString()
    {
        $writer = new \Bramus\Ansi\Writers\BufferWriter;
        $ansi = new \Bramus\Ansi\Ansi($writer);
        $ansi->color([SGR::COLOR_FG_WHITE])
             ->text(__CLASS__)
             ->color([SGR::COLOR_FG_WHITE])
             ->text(': [')
             ->color([SGR::COLOR_FG_YELLOW])
             ->text($this->code)
             ->color([SGR::COLOR_FG_WHITE])
             ->text('] (parameter: ')
             ->color([SGR::COLOR_FG_YELLOW])
             ->text(($this->parameter != null?$this->parameter->name:'UNKNOWN'))
             ->color([SGR::COLOR_FG_WHITE])
             ->text(') : ')
             ->color([SGR::COLOR_FG_RED])
             ->text($this->message.PHP_EOL)
             ->noStyle();

        return $writer->flush();
    }

    /**
     * Convert this Exception to a string.
     * 
     * @return string
     */
    public function __toString()
    {
        return __CLASS__.': ['.$this->code.'] (parameter: '.($this->parameter != null?$this->parameter->name:'UNKNOWN').') : '.$this->message.PHP_EOL;
    }
}