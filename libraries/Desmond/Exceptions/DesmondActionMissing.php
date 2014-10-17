<?php

class DesmondActionMissing extends Exception 
{ 
    public function __construct($controller, $action, $message = 'Action missing') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message . ": $controller@$action", 0); 
    }     

}

?>


