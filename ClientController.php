<?php

class ClientController extends Controller
{
    private $client;
    public function __construct()
    {
        $this->client = $this->model("Client");
    }

    public function index()
    {
        try {
            $clients = $this->client->all();
            $this->view('clients/index', [
                'clients' => $clients
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }
}
