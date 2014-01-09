<?php

class DesmondModuleMissingException extends Exception 
{ 
    public function __construct($message = 'Module Cannot be found') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message, 0); 
    }     

}

?>
