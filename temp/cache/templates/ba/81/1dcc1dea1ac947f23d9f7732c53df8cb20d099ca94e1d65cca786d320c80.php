<?php

/* home/index.html */
class __TwigTemplate_ba811dcc1dea1ac947f23d9f7732c53df8cb20d099ca94e1d65cca786d320c80 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
  <head>
    <meta charset=\"utf-8\">
    <title>Red - Add Color to PHP</title>

    <style>
 \thtml,body {
 \t\tmargin:0;
 \t\tpadding:0;
 \t\twidth:100%;
 \t\theight:100%;
 \t}
    body {
    \tdisplay:table;
    }

 

    p {
    \tdisplay:table-cell;
    \ttext-align:center;
    \tvertical-align:middle;
    }
    img {
    \t   transition: opacity .50s ease-in-out;
   -moz-transition: opacity .50s ease-in-out;
   -webkit-transition: opacity .50s ease-in-out;
    }
    img:hover {
    \topacity:0.3;
    }

    </style>
  </head>

<body style='background-color:#161616;overflow:hidden'>
\t    <p><img src='/assets/default/images/redlogo.png' alt='logo'/></p>
</body>

</html>";
    }

    public function getTemplateName()
    {
        return "home/index.html";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
