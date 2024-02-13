<?php


namespace StefGodin\NoTmpl\Engine;

use Exception;

class EngineException extends Exception
{
    const CTX_NO_CONTEXT = 1;
    const CTX_NO_OPEN_TAG = 2;
    const CTX_INVALID_OPEN_TAG = 3;
    const CTX_INVALID_NAME = 4;
    const OB_INVALID_STATE = 101;
    const TMPLRES_FILE_NOT_FOUND = 201;
    
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}