<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Supplier extends RestController
{
    private $validator;
    private $payload;
    public function __construct()
    {
        parent::__construct();
        $this->validator = new Validator();
        $this->load->model('GlobalModel');
        $this->payload = JWT_Verif_Access();
        if ($this->payload['status'] == false) {
            $res = formatResponse(400, [], [], $this->payload['message'], [], '');
            $this->response($res, 400);
        }
    }

    public function all_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RS', 'CPO', 'UPO', 'DS', 'US']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->GlobalModel->getData('supplier', ['deleteAt' => NULL]);
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RS', 'CPO', 'UPO', 'DS', 'US']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $supplier_id = $this->get('id');
        if ($supplier_id == NULL) {
            $res = formatResponse(400, [], [], 'ID supplier is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->GlobalModel->getData('supplier', ['supplier_id' => $supplier_id, 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data supplier not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'supplier_name' => $this->post('supplier_name'),
            'supplier_phone' => $this->post('supplier_phone'),
            'supplier_address' => $this->post('supplier_address'),
        );

        $make = $this->validator->make($data, [
            'supplier_name' => 'required',
            'supplier_phone' => 'required|numeric',
            'supplier_address' => 'required',
        ]);

        $make->setAliases([
            'supplier_name' => 'Name',
            'supplier_phone' => 'Phone',
            'supplier_address' => 'Address',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->insert('supplier', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('supplier', ['supplier_id' => $this->db->insert_id()], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create supplier');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create supplier', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['US']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $supplier_id = $this->get('id');
        if ($supplier_id == NULL) {
            $res = formatResponse(400, [], [], 'ID supplier is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('supplier', ['deleteAt' => NULL, 'supplier_id' => $supplier_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data supplier not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'supplier_name' => $this->put('supplier_name'),
            'supplier_phone' => $this->put('supplier_phone'),
            'supplier_address' => $this->put('supplier_address'),
        );

        $make = $this->validator->make($data, [
            'supplier_name' => 'required',
            'supplier_phone' => 'required|numeric',
            'supplier_address' => 'required',
        ]);

        $make->setAliases([
            'supplier_name' => 'Name',
            'supplier_phone' => 'Phone',
            'supplier_address' => 'Address',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {

            $cek = $this->GlobalModel->update('supplier', $data, ['supplier_id' => $supplier_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('supplier', ['supplier_id' => $supplier_id], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update supplier');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update supplier', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $supplier_id = $this->get('id');
        if ($supplier_id == NULL) {
            $res = formatResponse(400, [], [], 'ID supplier is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('supplier', ['deleteAt' => NULL, 'supplier_id' => $supplier_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data supplier not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('supplier', ['supplier_id' => $supplier_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete supplier');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed to delete supplier', [], '');
            $this->response($res, 400);
        }
    }
}
