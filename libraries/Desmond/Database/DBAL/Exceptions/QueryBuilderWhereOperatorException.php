<?php

class QueryBuilderWhereOperatorException extends Exception 
{ 
    public function __construct($message = 'SQL Where operator unknown') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message, 0); 
    }     

}

?>
