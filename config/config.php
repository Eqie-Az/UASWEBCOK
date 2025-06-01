<?php
// Configuration file for common settings
// This should be included at the top of all pages

// Base paths
$webRoot = '/UASWEBCOK/';
$basePath = $webRoot . 'views/';
$controllerPath = $webRoot . 'controllers/';

// Functions to generate URLs
function url($path)
{
    global $webRoot;
    return $webRoot . $path;
}

function asset($path)
{
    global $webRoot;
    return $webRoot . 'assets/' . $path;
}

