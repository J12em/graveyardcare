<?php

class LotTypeController extends Controller
{
    private $lotType;
    private $lotPrice;
    private $imgDir = "../public/images/lot-types";
    public function __construct()
    {
        $this->lotType = $this->model("LotType");
        $this->lotPrice = $this->model("LotPrice");
    }

    public function index()
    {
        try {
            $lotTypes = $this->lotType->all();
            $this->view('lot-types/index', [
                'lotTypes' => $lotTypes
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function create()
    {
        try {
            $this->view('lot-types/create');
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $lotType = $this->lotType->find($id);
            $this->view('lot-types/edit', [
                'lotType' => $lotType
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function show($id)
    {
        try {
            $lotType = $this->lotType->find($id);
            $this->view('lot-types/show', [
                'lotType' => $lotType
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function store()
    {
        try {
            $this->lotType->beginTransaction();

            $image_status = $this->uploadImage($this->files("image"), $this->imgDir);
            $form = [
                'name' => $this->input('name'),
                'description' => $this->input('description'),
                'cash_price' => json_encode($this->input('cash_price')),
                'at_need' => json_encode($this->input('at_need')),
                'image' => $image_status['status'] ? $image_status['image'] : ''
            ];
            $lot_type_id = $this->lotType->add($form, true);
            $this->storeLotPrices($lot_type_id);

            $this->session_put('success', 'Successfully Added!');

            $this->lotType->commitTransaction();
            $this->redirect('lot-types');
        } catch (Exception $e) {
            $this->lotType->rollbackTransaction();
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }

    public function storeLotPrices($lot_type_id)
    {
        foreach ($this->lotPrice->plans as $plan => $plan_name) {
            foreach ($this->lotPrice->dps as $dp_percent => $dp_name) {
                foreach ($this->lotPrice->terms as $term => $term_name) {
                    $this->lotPrice->add([
                        'lot_type_id' => $lot_type_id,
                        'plan' => $plan,
                        'term' => $term,
                        'dp_percent' => $dp_percent,
                        'lot_price' => $this->input('lot_price_' . $plan . '_' . $dp_percent . '_' . $term, 0),
                        'perpetual_care_fee' => $this->input('care_fee_' . $plan . '_' . $dp_percent . '_' . $term, 0),
                        'dp_amount' => $dp_percent > 0 ? $this->input('dp_amount_' . $plan . '_' . $dp_percent . '_' . $term, 0) : 0,
                        'monthly' => $this->input('monthly_' . $plan . '_' . $dp_percent . '_' . $term, 0),
                    ]);
                }
            }
        }
    }

    public function update($id)
    {
        try {
            $form = [
                'name' => $this->input('name'),
                'description' => $this->input('description'),
            ];

            if ($this->lotType->edit($form, $id)) {
                $this->session_put('success', 'Successfully Updated!');
            } else {
                $this->session_put('error', 'Error occur!');
            }
            $this->redirect('lot-types');
        } catch (Exception $e) {
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }


    public function destroy()
    {
        try {
            if ($this->lotType->remove($this->input('id'))) {
                $this->session_put('success', 'Successfully Deleted!');
            } else {
                $this->session_put('error', 'Error occur!');
            }
            $this->redirect('lot-types');
        } catch (Exception $e) {
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }
}
