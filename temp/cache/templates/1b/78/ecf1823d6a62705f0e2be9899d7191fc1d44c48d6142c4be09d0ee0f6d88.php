<?php

/* home/index.html */
class __TwigTemplate_1b78ecf1823d6a62705f0e2be9899d7191fc1d44c48d6142c4be09d0ee0f6d88 extends Twig_Template
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
     <script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js\"></script>
     <script src=\"";
        // line 36
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["app"]) ? $context["app"] : null), "Path", array(0 => "web"), "method"), "html", null, true);
        echo "/assets/default/js/red.js\"></script>

  </head>

<body style='background-color:#161616;overflow:hidden'>

<h1 style='color:white;'>";
        // line 42
        echo twig_escape_filter($this->env, (isset($context["go"]) ? $context["go"] : null), "html", null, true);
        echo "</h1>
\t    <p><img class='test' src='";
        // line 43
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["app"]) ? $context["app"] : null), "Path", array(0 => "web"), "method"), "html", null, true);
        echo "/assets/default/images/redlogo.png' alt='logo'/></p>
<input type='hidden' value='test' id='test_name'>
</body>

</html>
";
    }

    public function getTemplateName()
    {
        return "home/index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 43,  65 => 42,  56 => 36,  19 => 1,);
    }
}
