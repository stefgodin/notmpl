<?php


namespace Stefmachine\NoTmpl\Exception;

enum RenderError: int
{
    case CMP_INVALID_STATE = 1;
    
    case CMPSTACK_CMP_NOT_FOUND = 51;
    
    case OB_INVALID_STATE = 101;
    case OB_FILE_NOT_FOUND = 102;
    
    case OBSTACK_OB_NOT_FOUND = 151;
    
    case SLOTMAN_INVALID_SLOT_STATE = 201;
    case SLOTMAN_SLOT_NOT_FOUND = 202;
    
    case TMPLRES_FILE_NOT_FOUND = 251;
}
