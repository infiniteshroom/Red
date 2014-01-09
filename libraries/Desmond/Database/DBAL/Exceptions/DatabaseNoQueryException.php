<?php

class DatabaseNoQueryException extends Exception 
{ 
    public function __construct($message = 'Query returned zero rows') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message, 0); 
    }     

}

?>
