<?php

class CemeteryBlockController extends Controller
{
    private $cemeteryBlock;
    private $cemetery;
    private $lot;
    private $lotType;
    public function __construct()
    {
        $this->cemeteryBlock = $this->model("CemeteryBlock");
        $this->cemetery = $this->model("Cemetery");
        $this->lot = $this->model("Lot");
        $this->lotType = $this->model("LotType");
    }

    public function create($cemetery_id)
    {
        try {
            $cemetery = $this->cemetery->find($cemetery_id,['blocks']);
            $lotTypes = $this->lotType->all();
            $this->view('blocks/create', [
                'cemetery' => $cemetery,
                'lotTypes' => $lotTypes,
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
            $lot_type_id = $this->input('lot_type_id');
            $cemetery_id = $this->input('cemetery_id');
            $block_no = $this->input('name');
            $lots = (int) $this->input('lots');
            $form = [
                'name' => $block_no,
                'cemetery_id' => $cemetery_id,
                'lot_type_id' => $lot_type_id,
                'lots' => $lots,
                'coordinates' => $this->input('coordinates'),
                'section_name' => $this->input('section_name'),
                'color' => $this->input('color'),
            ];

            $block_id = $this->cemeteryBlock->add($form, true);
            if (!$block_id) {
                throw new Exception("Error occur");
            }
            for ($lotNo = 1; $lotNo <= $lots; $lotNo++) {
                $form = [
                    'name' => $lotNo,
                    'block_no' => $block_no,
                    'block_id' => $block_id,
                    'cemetery_id' => $cemetery_id,
                    'lot_type_id' => $lot_type_id,
                ];
                $this->lot->add($form);
            }
            $this->session_put('success', 'Successfully Added!');
            return [
                'status' => true,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'errors' => $e
            ];
        }
    }
}
