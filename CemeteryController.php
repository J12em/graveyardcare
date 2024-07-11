<?php

class CemeteryController extends Controller
{
    private $cemetery;
    private $cemeteryBlock;
    private $lotType;
    private $imgDir = "../public/images/cemeteries";
    public function __construct()
    {
        $this->cemetery = $this->model("Cemetery");
        $this->cemeteryBlock = $this->model("CemeteryBlock");
        $this->lotType = $this->model("LotType");
    }

    public function index()
    {
        try {
            $cemeteries = $this->cemetery->all();
            $this->view('cemetery/index', [
                'cemeteries' => $cemeteries
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
            $this->view('cemetery/create');
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function show($cemetery_id)
    {
        try {
            $cemetery = $this->cemetery->find($cemetery_id, ['blocks', 'lots']);
            $this->view('cemetery/show', [
                'cemetery' => $cemetery
            ]);
        } catch (Exception $e) {
            $this->view('403/index', [
                'errors' => $e
            ]);
        }
    }

    public function edit($cemetery_id)
    {
        try {
            $cemetery = $this->cemetery->find($cemetery_id);
            $this->view('cemetery/edit', [
                'cemetery' => $cemetery
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
            $image_status = $this->uploadImage($this->files("image"), $this->imgDir);
            $form = [
                'name' => $this->input('name'),
                'address' => $this->input('address'),
                'description' => $this->input('description'),
                'coordinates' => $this->input('coordinates'),
                'image' => $image_status['status'] ? $image_status['image'] : ''
            ];

            if ($this->cemetery->add($form)) {
                $this->session_put('success', 'Successfully Added!');
            } else {
                $this->session_put('error', 'Error occur!');
            }
            $this->redirect('cemeteries');
        } catch (Exception $e) {
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }

    public function update($cemetery_id)
    {
        try {
            $form = [
                'name' => $this->input('name'),
                'address' => $this->input('address'),
            ];

            if ($this->cemetery->edit($form, $cemetery_id)) {
                $this->session_put('success', 'Successfully Updated!');
            } else {
                $this->session_put('error', 'Error occur!');
            }
            $this->redirect('cemeteries');
        } catch (Exception $e) {
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }


    public function destroy()
    {
        try {
            if ($this->cemetery->remove($this->input('id'))) {
                $this->session_put('success', 'Successfully Deleted!');
            } else {
                $this->session_put('error', 'Error occur!');
            }
            $this->redirect('cemeteries');
        } catch (Exception $e) {
            $this->view('500/index', [
                'errors' => $e
            ]);
        }
    }
}
