<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Designator extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RPRO', 'CPRO', 'UPRO', 'DPRO', 'APRO', 'DEPRO', 'CTEC', 'UTEC', 'CUSI', 'CFED', 'UFED', 'DFED', 'CDIS', 'UDIS', 'DDIS', 'CFLS', 'DFLS', 'CKHSL', 'UKHSL', 'DKHSL', 'CTKHS', 'CMKHS', 'CSAI', 'CFLI', 'DFLI', 'CSAT', 'CFLT', 'DFLT', 'CSV3', 'CDSV3', 'CSL', 'CFLL', 'DFLL', 'CSV4', 'CDV4', 'CSTD', 'URECON', 'CSTP', 'CPTS', 'CRTP', 'RD', 'CDP', 'DDP', 'CDPP', 'DDPP', 'DD', 'UD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->db->join('product', 'product.product_id=designator.product_id')->get_where('designator', ['designator.deleteAt' => NULL])->result_array();
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RPRO', 'CPRO', 'UPRO', 'DPRO', 'APRO', 'DEPRO', 'CTEC', 'UTEC', 'CUSI', 'CFED', 'UFED', 'DFED', 'CDIS', 'UDIS', 'DDIS', 'CFLS', 'DFLS', 'CKHSL', 'UKHSL', 'DKHSL', 'CTKHS', 'CMKHS', 'CSAI', 'CFLI', 'DFLI', 'CSAT', 'CFLT', 'DFLT', 'CSV3', 'CDSV3', 'CSL', 'CFLL', 'DFLL', 'CSV4', 'CDV4', 'CSTD', 'URECON', 'CSTP', 'CPTS', 'CRTP', 'RD', 'CDP', 'DDP', 'CDPP', 'DDPP', 'DD', 'UD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $designator_id = $this->get('id');
        if ($designator_id == NULL) {
            $res = formatResponse(400, [], [], 'ID designator is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->db->join('product', 'product.product_id=designator.product_id')->get_where('designator', ['designator.designator_id' => $designator_id, 'designator.deleteAt' => NULL])->result_array();
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data designator not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'designator_code' => $this->post('designator_code'),
            'designator_desc' => $this->post('designator_desc'),
            'product_id' => $this->post('product_id'),
        );

        $make = $this->validator->make($data, [
            'designator_code' => 'required',
            'designator_desc' => 'required',
            'product_id' => 'required',
        ]);

        $make->setAliases([
            'designator_code' => 'Designator Code',
            'designator_desc' => 'Designator Description',
            'product_id' => 'Product',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('product', ['deleteAt' => NULL, 'product_id' => $data['product_id']], false);
            if ($getData == NULL) {
                $res = formatResponse(404, [], [], 'Data product not found', [], '');
                $this->response($res, 404);
            }
            // $check = $this->GlobalModel->getData('designator', ['deleteAt' => NULL, 'product_id' => $data['product_id']], false);
            // if ($check != NULL) {
            //     $res = formatResponse(404, [], [], 'Data product used', [], '');
            //     $this->response($res, 404);
            // }
            $cek = $this->GlobalModel->insert('designator', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('designator', ['designator_id' => $this->db->insert_id()], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create designator');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create designator', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $designator_id = $this->get('id');
        if ($designator_id == NULL) {
            $res = formatResponse(400, [], [], 'ID designator is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('designator', ['deleteAt' => NULL, 'designator_id' => $designator_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data designator not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'designator_code' => $this->put('designator_code'),
            'designator_desc' => $this->put('designator_desc'),
            'product_id' => $this->put('product_id'),
        );

        $make = $this->validator->make($data, [
            'designator_code' => 'required',
            'designator_desc' => 'required',
            'product_id' => 'required',
        ]);

        $make->setAliases([
            'designator_code' => 'Designator Code',
            'designator_desc' => 'Designator Description',
            'product_id' => 'Product',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getDataProduct = $this->GlobalModel->getData('product', ['deleteAt' => NULL, 'product_id' => $data['product_id']], false);
            if ($getDataProduct == NULL) {
                $res = formatResponse(404, [], [], 'Data product not found', [], '');
                $this->response($res, 404);
            }
            // $check = $this->GlobalModel->getData('designator', ['deleteAt' => NULL, 'product_id' => $data['product_id'], 'designator_id !=' => $designator_id], false);
            // if ($check != NULL) {
            //     $res = formatResponse(404, [], [], 'Data product used', [], '');
            //     $this->response($res, 404);
            // }
            $cek = $this->GlobalModel->update('designator', $data, ['designator_id' => $designator_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('designator', ['designator_id' => $designator_id], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update designator');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update designator', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $designator_id = $this->get('id');
        if ($designator_id == NULL) {
            $res = formatResponse(400, [], [], 'ID designator is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('designator', ['deleteAt' => NULL, 'designator_id' => $designator_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data designator not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('designator', ['designator_id' => $designator_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete designator');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed to delete designator', [], '');
            $this->response($res, 400);
        }
    }

    public function productNotUsed_get()
    {
        $designator_id = $this->get('id');
        $getData = $this->GlobalModel->getData('product', ['deleteAt' => NULL]);
        $return = [];
        foreach ($getData as $k => $v) {
            if ($designator_id == NULL || $designator_id == '') {
                $where = ['deleteAt' => NULL, 'product_id' => $v['product_id']];
            } else {
                $where = ['deleteAt' => NULL, 'product_id' => $v['product_id'], 'designator_id !=' => $designator_id];
            }
            $check = $this->GlobalModel->getData('designator', $where, false);
            if ($check == NULL) {
                $return[] = [
                    'product_id' => $v['product_id'],
                    'product_name' => $v['product_name'],
                    'product_portion' => $v['product_portion'],
                ];
            }
        }
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }
}
