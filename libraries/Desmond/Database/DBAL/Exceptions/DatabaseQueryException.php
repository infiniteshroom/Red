<?php

class DatabaseQueryException extends Exception 
{ 
    public function __construct($message = 'Query Error') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message, 0); 
    }     

}

?>
