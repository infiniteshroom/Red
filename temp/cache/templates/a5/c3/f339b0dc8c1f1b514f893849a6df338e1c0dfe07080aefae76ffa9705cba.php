<?php

/* home/index.html */
class __TwigTemplate_a5c3f339b0dc8c1f1b514f893849a6df338e1c0dfe07080aefae76ffa9705cba extends Twig_Template
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
\t    <p><img src='";
        // line 38
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["app"]) ? $context["app"] : null), "Path", array(0 => "web"), "method"), "html", null, true);
        echo "/assets/default/images/redlogo.png' alt='logo'/></p>
</body>

</html>";
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
        return array (  58 => 38,  19 => 1,);
    }
}
