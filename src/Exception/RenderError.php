<?php


namespace StefGodin\NoTmpl\Exception;

enum RenderError: int
{
    case CTX_NO_CONTEXT = 1;
    case CTX_NO_OPEN_TAG = 2;
    case CTX_INVALID_OPEN_TAG = 3;
    case CTX_INVALID_NAME = 4;
    
    case OB_INVALID_STATE = 101;
    
    case TMPLRES_FILE_NOT_FOUND = 201;
}
