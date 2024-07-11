<?php

class ApiController extends Controller
{
    public function getData()
    {
        $db = $this->model('Database');
        $data = $db->getSomething();
        $this->response($data);
    }
}
