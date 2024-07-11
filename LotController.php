<?php

class LotController extends Controller
{
    private $lot;
    private $client;
    private $reservation;
    private $payment;
    private $benefeciary;
    private $imgDir = "../public/images/lot-types";
    public function __construct()
    {
        $this->lot = $this->model("Lot");
        $this->client = $this->model("Client");
        $this->reservation = $this->model("Reservation");
        $this->payment = $this->model("Payment");
        $this->benefeciary = $this->model("Benefeciary");
    }

    public function plot($lot_id)
    {
        try {
            $lot = $this->lot->find($lot_id, ['cemetery', 'lot_type', 'block']);
            $this->view('lots/plot', [
                'lot' => $lot
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function show($lot_id)
    {
        try {
            $lot = $this->lot->find($lot_id, ['cemetery', 'lot_type', 'block']);
            $this->view('lots/show', [
                'lot' => $lot
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function reserve($lot_id)
    {
        if (!isset($_SESSION[SYSTEM]['user_id'])) {
            $_SESSION[SYSTEM]['last_url'] = "cemeteries/lots/$lot_id/reserve";
            $this->session_put('error', 'You have to login first, before doing reservation');
            $this->redirectLogin();
        }
        try {
            $lot = $this->lot->find($lot_id, ['cemetery', 'lot_type', 'block']);
            $client = $this->client->findByUserId($_SESSION[SYSTEM]['user_id']);
            $this->view('lots/reserve', [
                'lot' => $lot,
                'client' => $client
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function storeReservation($lot_id)
    {
        try {
            $this->lot->beginTransaction();

            $client_id = $this->input('client_id');
            $cemetery_id = $this->input('cemetery_id');

            // UPDATE CLIENT DATA
            $formClient = [
                'user_id' => $_SESSION[SYSTEM]['user_id'],
                'first_name' => $this->input('first_name'),
                'middle_name' => $this->input('middle_name'),
                'last_name' => $this->input('last_name'),
                'address' => $this->input('address'),
                'dob' => $this->input('dob'),
                'gender' => $this->input('gender'),
                'contact_no' => $this->input('contact_no'),
                'email' => $this->input('email'),
                'civil_status' => $this->input('civil_status'),
            ];
            $this->client->edit($formClient, $client_id);

            // ADD RESERVATION DATA
            $reservation_id = $this->reservation->add([
                'status' => 'PENDING',
                'client_id' => $client_id,
                'cemetery_id' => $cemetery_id,
                'lot_id' => $lot_id,
                'reservation_date' => date('Y-m-d'),
                'expires_at' => date('Y-m-d', strtotime('+15 days'))
            ], true);

            // ADD BENEFECIARIES
            $this->benefeciary->add([
                'client_id' => $client_id,
                'lot_id' => $lot_id,
                'name' => $this->input('benefeciary_name'),
                'dob' => $this->input('benefeciary_dob'),
                'relationship' => $this->input('benefeciary_relationship'),
            ]);

            // UPDATE LOT STATUS
            $this->lot->edit([
                'status' => 'RESERVED'
            ], $lot_id);

            // ADD PAYMENT DATA
            $this->payment->add([
                'client_id' => $client_id,
                'reservation_id' => $reservation_id,
                'channel' => $this->input('payment_channel'),
                'account_name' => $this->input('payment_account_name'),
                'account_number' => $this->input('payment_account_number'),
                'reference_number' => $this->input('payment_reference_no'),
                'amount' => $this->input('payment_amount'),
            ]);

            // ADD BENEFICIARY DATA


            $this->lot->commitTransaction();
            $this->redirect('reservations');
        } catch (Exception $e) {
            $this->lot->rollbackTransaction();
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }
}
