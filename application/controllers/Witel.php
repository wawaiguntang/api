<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Witel extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RW', 'DW', 'CWU', 'DWU']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->db
            ->select('p.witel_id,p.witel_name,p.witel_code,b.region_name,p.createAt,p.updateAt,p.deleteAt')
            ->join('region b', 'b.region_id=p.region_id')
            ->where(['p.deleteAt' => NULL])
            ->get('witel p')
            ->result_array();
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RW', 'DW', 'CWU', 'DWU']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_id = $this->get('id');
        if ($witel_id == NULL) {
            $res = formatResponse(400, [], [], 'ID witel is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->db
            ->select('p.witel_id,p.witel_name,p.witel_code,b.region_name,p.createAt,p.updateAt,p.deleteAt')
            ->join('region b', 'b.region_id=p.region_id')
            ->where(['p.deleteAt' => NULL, 'p.witel_id' => $witel_id])
            ->get('witel p')
            ->row_array();
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data witel not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'witel_name' => $this->post('witel_name'),
            'witel_code' => $this->post('witel_code'),
            'region_id' => $this->post('region_id'),
        );

        $make = $this->validator->make($data, [
            'witel_name' => 'required',
            'witel_code' => 'required',
            'region_id' => 'required',
        ]);

        $make->setAliases([
            'witel_name' => 'Witel Name',
            'witel_code' => 'Witel Code',
            'region_id' => 'Region',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('region', ['region_id' => $data['region_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data region not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->insert('witel', $data);
            if ($cek) {
                $data =
                    $this->db
                    ->select('p.witel_id,p.witel_name,p.witel_code,b.region_name,p.createAt,p.updateAt,p.deleteAt')
                    ->join('region b', 'b.region_id=p.region_id')
                    ->where(['p.deleteAt' => NULL, 'p.witel_id' => $this->db->insert_id()])
                    ->get('witel p')
                    ->row_array();
                $res = formatResponse(200, $data, [], '', [], 'Success to create witel');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create witel', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_id = $this->get('id');
        if ($witel_id == NULL) {
            $res = formatResponse(400, [], [], 'ID witel is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('witel', ['deleteAt' => NULL, 'witel_id' => $witel_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data witel not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'witel_name' => $this->put('witel_name'),
            'witel_code' => $this->put('witel_code'),
            'region_id' => $this->put('region_id'),
        );

        $make = $this->validator->make($data, [
            'witel_name' => 'required',
            'witel_code' => 'required',
            'region_id' => 'required',
        ]);

        $make->setAliases([
            'witel_name' => 'Witel Name',
            'witel_code' => 'Witel Code',
            'region_id' => 'Region',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('region', ['region_id' => $data['region_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data region not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('witel', $data, ['witel_id' => $witel_id]);
            if ($cek) {
                $data = $this->db
                    ->select('p.witel_id,p.witel_name,p.witel_code,b.region_name,p.createAt,p.updateAt,p.deleteAt')
                    ->join('region b', 'b.region_id=p.region_id')
                    ->where(['p.deleteAt' => NULL, 'p.witel_id' => $witel_id])
                    ->get('witel p')
                    ->row_array();
                $res = formatResponse(200, $data, [], '', [], 'Success to update witel');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update witel', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_id = $this->get('id');
        if ($witel_id == NULL) {
            $res = formatResponse(400, [], [], 'ID witel is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('witel', ['deleteAt' => NULL, 'witel_id' => $witel_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data witel not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('witel', ['witel_id' => $witel_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete witel');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed to delete witel', [], '');
            $this->response($res, 400);
        }
    }

    public function user_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RWUU']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_id = $this->get('id');
        if ($witel_id == NULL) {
            $res = formatResponse(400, [], [], 'ID witel is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('witel', ['witel_id' => $witel_id, 'deleteAt' => NULL], false);
        $return = $getData;
        $getUser = $this->db
            ->select('dp.witel_user_id,d.userCode,d.name,d.photo,d.email,d.createAt,d.updateAt,d.deleteAt')
            ->join('user d', 'd.userCode=dp.userCode')
            ->where(['dp.deleteAt' => NULL, 'dp.witel_id' => $witel_id])
            ->get('witel_user dp')
            ->result_array();
        $return['user'] = $getUser;
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }
}
