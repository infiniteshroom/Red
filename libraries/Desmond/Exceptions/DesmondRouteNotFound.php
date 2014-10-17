<?php

class DesmondRouteNotFound extends Exception 
{ 
    public function __construct($route='/', $message = 'Route not found') 
    { 
        /* call the super class Exception constructor */ 
        parent::__construct($message . ' '. $route, 0); 
    }     

}

?>
