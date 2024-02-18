<?php


namespace StefGodin\NoTmpl\Engine;

use Exception;

class EngineException extends Exception
{
    
    const NO_CONTEXT = 101;
    const ILLEGAL_BUFFER_ACTION = 201;
    const INVALID_TAG_STRUCTURE = 301;
    const FILE_NOT_FOUND = 401;
}