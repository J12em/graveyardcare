<?php

class PaymentController extends Controller
{
    private $payment;
    private $block;
    private $lotType;
    private $imgDir = "../public/images/lot-types";
    public function __construct()
    {
        $this->payment = $this->model("Payment");
        $this->block = $this->model("CemeteryBlock");
        $this->lotType = $this->model("LotType");
    }

    public function index()
    {
        try {
            $payments = $this->payment->all(['client', 'reservation']);
            $this->view('payments/index', [
                'payments' => $payments
            ]);
        } catch (Exception $e) {
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }
}
