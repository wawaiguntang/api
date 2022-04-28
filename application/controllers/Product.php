<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Product extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RPP', 'CDP', 'DDP', 'CPOI', 'UPOI', 'DPP', 'UPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,p.createAt,p.updateAt,p.deleteAt')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where(['p.deleteAt' => NULL])
            ->get('product p')
            ->result_array();
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RPP', 'CDP', 'DDP', 'CPOI', 'UPOI', 'DPP', 'UPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $product_id = $this->get('id');
        if ($product_id == NULL) {
            $res = formatResponse(400, [], [], 'ID product is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,p.createAt,p.updateAt,p.deleteAt')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where(['p.deleteAt' => NULL, 'p.product_id' => $product_id])
            ->get('product p')
            ->row_array();
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data product not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'product_name' => $this->post('product_name'),
            'product_portion' => $this->post('product_portion'),
            'brand_id' => $this->post('brand_id'),
        );

        $make = $this->validator->make($data, [
            'product_name' => 'required',
            'product_portion' => 'required',
            'brand_id' => 'required',
        ]);

        $make->setAliases([
            'product_name' => 'Product Name',
            'product_portion' => 'Portion',
            'brand_id' => 'Brand',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('brand', ['brand_id' => $data['brand_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data brand not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->insert('product', $data);
            if ($cek) {
                $data =
                    $this->db
                    ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,p.createAt,p.updateAt,p.deleteAt')
                    ->join('brand b', 'b.brand_id=p.brand_id')
                    ->where(['p.deleteAt' => NULL, 'p.product_id' => $this->db->insert_id()])
                    ->get('product p')
                    ->row_array();
                $res = formatResponse(200, $data, [], '', [], 'Success to create product');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create product', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $product_id = $this->get('id');
        if ($product_id == NULL) {
            $res = formatResponse(400, [], [], 'ID product is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('product', ['deleteAt' => NULL, 'product_id' => $product_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data product not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'product_name' => $this->put('product_name'),
            'product_portion' => $this->put('product_portion'),
            'brand_id' => $this->put('brand_id'),
        );

        $make = $this->validator->make($data, [
            'product_name' => 'required',
            'product_portion' => 'required',
            'brand_id' => 'required',
        ]);

        $make->setAliases([
            'product_name' => 'Product Name',
            'product_portion' => 'Portion',
            'brand_id' => 'Brand',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('brand', ['brand_id' => $data['brand_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data brand not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('product', $data, ['product_id' => $product_id]);
            if ($cek) {
                $data = $this->db
                    ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,p.createAt,p.updateAt,p.deleteAt')
                    ->join('brand b', 'b.brand_id=p.brand_id')
                    ->where(['p.deleteAt' => NULL, 'p.product_id' => $product_id])
                    ->get('product p')
                    ->row_array();
                $res = formatResponse(200, $data, [], '', [], 'Success to update product');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update product', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $product_id = $this->get('id');
        if ($product_id == NULL) {
            $res = formatResponse(400, [], [], 'ID product is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('product', ['deleteAt' => NULL, 'product_id' => $product_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data product not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('product', ['product_id' => $product_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete product');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed to delete product', [], '');
            $this->response($res, 400);
        }
    }
}
