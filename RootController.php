<?php

class RootController extends Controller
{
    public function notFound()
    {
        $this->view('404/index', []);
    }
}
