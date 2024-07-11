<?php

class ReservationController extends Controller
{
    private $reservation;
    private $block;
    private $lotType;
    private $imgDir = "../public/images/lot-types";
    public function __construct()
    {
        $this->reservation = $this->model("Reservation");
        $this->block = $this->model("CemeteryBlock");
        $this->lotType = $this->model("LotType");
    }

    public function index()
    {
        try {
            $reservations = $this->reservation->all(['cemetery', 'client', 'lot']);
            $this->view('reservations/index', [
                'reservations' => $reservations
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }
}
