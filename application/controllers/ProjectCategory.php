<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class ProjectCategory extends RestController
{
    private $validator;
    private $payload;
    public function __construct()
    {
        parent::__construct();
        $this->validator = new Validator();
        $this->load->model('GlobalModel');
        // $this->payload = JWT_Verif_Access();
        // if ($this->payload['status'] == false) {
        //     $res = formatResponse(400, [], [], $this->payload['message'], [], '');
        //     $this->response($res, 400);
        // }
    }

    public function all_get()
    {
        // $permission = checkPermission($this->payload['data']['email'], ['RB', 'CPP', 'UPP', 'DB', 'UB']);
        // if ($permission['status'] == false) {
        //     $this->response($permission['data'], 400);
        // }
        
        $cat = $this->GlobalModel->getData('project_cat', ['deleteAt' => NULL]);
        // foreach ($cat as $k => $v) {
        //     $d = [
        //         'cat_id' => $v['cat_id'],
        //         'cat_name' => $v['cat_name'],
        //         'status' => ($v['cat_action'] == 0) ? 'disable' : 'active'
        //     ];
        //     if ($v['cat_parent'] == 0) {
        //         $return[$v['cat_id']] = $d;
        //     } else {
        //         $return[$v['cat_parent']]['sub_cat'][] = $d;
        //     }
        // }
        $cat = $this->buildTree($cat,0);
        $res = formatResponse(200, array_values($cat), [], '', [], '');
        $this->response($res, 200);
    }
    
    function buildTree(array $elements, $parentId = 0) {
        $branch = array();
    
        foreach ($elements as $element => $v) {
            if ($v['cat_parent'] == $parentId) {
                $children = $this->buildTree($elements, $v['cat_id']);
                if ($children) {
                    $v['sub_cat'] = array_values($children);
                }
                $branch[$v['cat_id']] = $v;
                unset($elements[$v['cat_id']]);
            }
        }
        return $branch;
    }
}
