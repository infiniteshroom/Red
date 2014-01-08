<?php

class QueryBuilderNoTableException extends Exception 
{ 
    public function __construct($message = 'No Table has been selected') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message, 0); 
    }     

}

?>
