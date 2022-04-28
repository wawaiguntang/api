<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class ReciveOrder extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RROPODO', 'USDOITP', 'USDOPTD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_id = ($this->get('witel') == NULL) ? NULL : $this->get('witel');
        if($witel_id != NULL){
            $where = ['witel_id' => $witel_id];
        }else{
            $where = [];
        }
        $return = [
            'do' => $this->db->where($where)->where('deleteAt', NULL)->where_in('do_status', ['processed', 'done'])->get('do')->result_array(),
            'po' => $this->db->where('deleteAt', NULL)->where_in('po_status', ['processed', 'done'])->get('po')->result_array(),
        ];
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    public function allByWitel_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RROPODOBW', 'USDOITP', 'USDOPTD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->join('witel ww', 'ww.witel_id=w.witel_id')->get_where('witel_user w', ['ww.deleteAt' => NULL, 'w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email']])->row_array();
        if ($check == NULL) {
            $res = formatResponse(400, [], [], 'You\'re don\'t have a witel', [], '');
            $this->response($res, 400);
        }
        $return = $this->db
            ->where('deleteAt', NULL)
            ->where_in('do_status', ['processed', 'done'])
            ->where('witel_id', $check['witel_id'])
            ->get('do')
            ->result_array();
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }
}
