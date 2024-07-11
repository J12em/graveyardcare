<?php

class View
{
    public static function render($view, $data = [])
    {
        require_once '../app/views/' . $view . '.php';
    }
}
