<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Brand extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RB', 'CPP', 'UPP', 'DB', 'UB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->GlobalModel->getData('brand', ['deleteAt' => NULL]);
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RB', 'CPP', 'UPP', 'DB', 'UB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $brand_id = $this->get('id');
        if ($brand_id == NULL) {
            $res = formatResponse(400, [], [], 'ID brand is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->GlobalModel->getData('brand', ['brand_id' => $brand_id, 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data brand not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'brand_name' => $this->post('brand_name'),
        );

        $make = $this->validator->make($data, [
            'brand_name' => 'required',
        ]);

        $make->setAliases([
            'brand_name' => 'Brand',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->insert('brand', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('brand', ['brand_id' => $this->db->insert_id()], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create brand');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create brand', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $brand_id = $this->get('id');
        if ($brand_id == NULL) {
            $res = formatResponse(400, [], [], 'ID brand is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('brand', ['deleteAt' => NULL, 'brand_id' => $brand_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data brand not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'brand_name' => $this->put('brand_name'),
        );

        $make = $this->validator->make($data, [
            'brand_name' => 'required',
        ]);

        $make->setAliases([
            'brand_name' => 'Brand',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {

            $cek = $this->GlobalModel->update('brand', $data, ['brand_id' => $brand_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('brand', ['brand_id' => $brand_id], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update brand');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update brand', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $brand_id = $this->get('id');
        if ($brand_id == NULL) {
            $res = formatResponse(400, [], [], 'ID brand is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('brand', ['deleteAt' => NULL, 'brand_id' => $brand_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data brand not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('brand', ['brand_id' => $brand_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete brand');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed to delete brand', [], '');
            $this->response($res, 400);
        }
    }
}
