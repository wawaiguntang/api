<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class WitelUser extends RestController
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


    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CWU']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'witel_id' => $this->post('witel_id'),
            'userCode' => $this->post('userCode'),
        );

        $make = $this->validator->make($data, [
            'witel_id' => 'required',
            'userCode' => 'required',
        ]);

        $make->setAliases([
            'witel_id' => 'Witel',
            'userCode' => 'User',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $witel = $this->GlobalModel->getData('witel', ['deleteAt' => NULL, 'witel_id' => $data['witel_id']]);
            if ($witel == NULL) {
                $res = formatResponse(400, [], [], 'Data witel not found', [], '');
                $this->response($res, 400);
            }
            $user = $this->GlobalModel->getData('user', ['deleteAt' => NULL, 'userCode' => $data['userCode']]);
            if ($user == NULL) {
                $res = formatResponse(400, [], [], 'Data user not found', [], '');
                $this->response($res, 400);
            }
            $cc = $this->GlobalModel->getData('witel_user', ['deleteAt' => NULL, 'userCode' => $data['userCode'], 'witel_id' => $data['witel_id']]);
            if ($cc != NULL) {
                $res = formatResponse(400, [], [], 'Data already exists', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->insert('witel_user', $data);
            if ($cek) {
                $res = formatResponse(200, [], [], '', [], 'Success add user to witel');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed add user to witel', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DWU']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_user_id = $this->get('id');
        if ($witel_user_id == NULL) {
            $res = formatResponse(400, [], [], 'ID witel user is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('witel_user', ['deleteAt' => NULL, 'witel_user_id' => $witel_user_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data witel user not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('witel_user', ['witel_user_id' => $witel_user_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success delete user from witel');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed delete user from witel', [], '');
            $this->response($res, 400);
        }
    }
}
