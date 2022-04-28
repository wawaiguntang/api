<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Account extends RestController
{
    private $validator;
    private $payload;
    public function __construct()
    {
        parent::__construct();
        $this->validator = new Validator();
        $this->load->model('UserModel');
        $this->load->model('ModuleModel');
        $this->load->model('RoleModel');
        $this->load->model('GlobalModel');
        $this->payload = JWT_Verif_Access();
        if ($this->payload['status'] == false) {
            $res = formatResponse(400, [], [], $this->payload['message'], [], '');
            $this->response($res, 400);
        }
    }
    public function allPermission_get()
    {
        $return = [];
        $role = $this->UserModel->allRoleByEmail($this->payload['data']['email']);
        $module = $this->ModuleModel->allModule();
        foreach ($role as $k => $v) {
            $roleDetail = $this->RoleModel->oneRole($v['roleCode']);
            $gg = [
                'roleCode' => $roleDetail['roleCode'],
                'role' => $roleDetail['role']
            ];
            $temp = $gg;
            foreach ($module as $m => $c) {
                $permission = $this->RoleModel->allRolePermission($v['roleCode'], $c['moduleCode']);
                if ($permission != NULL) {
                    $rr = [
                        'moduleCode' => $c['moduleCode'],
                        'module' => $c['module'],
                        'permission' => $permission
                    ];
                    $temp['module'][] = $rr;
                }
            }
            $spesialPermission = $this->UserModel->allUserPermission($v['userCode']);
            $temp['spesialPermission'] = $spesialPermission;
            $return[] = $temp;
        }
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    public function allPermissionNew_get()
    {
        $return = [];
        $role = $this->UserModel->allRoleByEmail($this->payload['data']['email']);
        $module = $this->ModuleModel->allModule();
        foreach ($role as $k => $v) {
            foreach ($module as $m => $c) {
                if (!isset($return[$c['module']])) {
                    $permission = $this->RoleModel->allRolePermission($v['roleCode'], $c['moduleCode']);
                    if ($permission != NULL) {
                        $return[$c['module']] = $permission;
                    }
                }
            }
        }
        $spesialPermission = $this->UserModel->allUserPermission($v['userCode']);
        $return['spesialPermission'] = $spesialPermission;
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    public function self_get()
    {
        $data = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        $res = formatResponse(200, [
            'userCode' => $data['userCode'],
            'name' => $data['name'],
            'email' => $data['email'],
            'photo' => $data['photo']
        ], [], '', [], '');
        $this->response($res, 200);
    }

    public function update_put()
    {
        $getData = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'name' => $this->put('name'),
            'email' => $this->put('email'),
            'photo' => $this->put('photo'),
        );

        $make = $this->validator->make($data, [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $make->setAliases([
            'name' => 'Name',
            'email' => 'Email',
            'photo' => 'Photo',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cekUnik = $this->UserModel->uniqueEmail($data['email']);
            if ($cekUnik != NUll) {
                if ($cekUnik['userCode'] != $getData['userCode']) {
                    $res = formatResponse(400, [], [
                        'email' => 'Email already used'
                    ], '', [], '');
                    $this->response($res, 400);
                }
            }
            $cek = $this->UserModel->editUser($data, ['userCode' => $getData['userCode']]);
            if ($cek) {
                $data = $this->UserModel->oneUser($getData['userCode']);
                $res = formatResponse(200, $data, [], 'Success to update account', [], '');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update account', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function changePassword_put()
    {
        $getData = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'password' => md5($this->put('password')),
        );

        $make = $this->validator->make($data, [
            'password' => 'required',
        ]);

        $make->setAliases([
            'password' => 'Password'
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->UserModel->editUser($data, ['userCode' => $getData['userCode']]);
            if ($cek) {
                $data = $this->UserModel->oneUser($getData['userCode']);
                $res = formatResponse(200, $data, [], 'Success to update password', [], '');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update password', [], '');
                $this->response($res, 400);
            }
        }
    }

    // public function witel_get()
    // {
    //     $getData = $this->GlobalModel->getData('user', ['email' => $this->payload['data']['email'], 'deleteAt' => NULL], false);
    //     $return = $getData;
    //     $getUser = $this->db
    //         ->select('d.witel_id,d.witel_code,d.witel_name')
    //         ->join('witel d', 'd.witel_id=dp.witel_id')
    //         ->where(['dp.deleteAt' => NULL, 'd.deleteAt' => NULL, 'dp.userCode' => $getData['userCode']])
    //         ->get('witel_user dp')
    //         ->result_array();
    //     $return['witel'] = $getUser;
    //     $res = formatResponse(200, $return, [], '', [], '');
    //     $this->response($res, 200);
    // }
}
