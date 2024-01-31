<?php


namespace Stefmachine\NoTmpl\Exception;

use Exception;

class RenderException extends Exception implements ExceptionInterface
{
    public function __construct(string $message, RenderError $error)
    {
        parent::__construct($message, $error->value);
    }
}