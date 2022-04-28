<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Permission extends RestController
{
    private $payload;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModuleModel');
        $this->load->model('PermissionModel');
        $this->payload = JWT_Verif_Access();
        if ($this->payload['status'] == false) {
            $res = formatResponse(400, [], [], $this->payload['message'], [], '');
            $this->response($res, 400);
        }
    }
    public function all_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RMP', 'CRP', 'DRP', 'CUP', 'DUP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $return = [];
        $module = $this->ModuleModel->allModule();
        foreach ($module as $m => $c) {
            $permission = $this->PermissionModel->permissionByModule($c['moduleCode']);
            if ($permission != NULL) {
                $rr = [
                    'moduleCode' => $c['moduleCode'],
                    'module' => $c['module'],
                    'permission' => $permission
                ];
                $return[] = $rr;
            }
        }
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RMP', 'CRP', 'DRP', 'CUP', 'DUP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $moduleCode = $this->get('id');
        if ($moduleCode == NULL) {
            $res = formatResponse(400, [], [], 'ID role is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->ModuleModel->oneModule($moduleCode);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data module not found', [], '');
            $this->response($res, 404);
        }


        $permission = $this->PermissionModel->permissionByModule($getData['moduleCode']);
        $return = [
            'moduleCode' => $getData['moduleCode'],
            'module' => $getData['module'],
            'permission' => $permission
        ];

        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }
}
