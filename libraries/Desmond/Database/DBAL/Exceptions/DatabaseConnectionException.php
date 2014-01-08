<?php

class DatabaseConnectionException extends Exception 
{ 
    public function __construct($message = 'Cannot connect to the database') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message, 0); 
    }     

}

?>
