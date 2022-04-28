<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Stock extends RestController
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

    public function allHO_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RSTOCKHO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $return = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,sh.stock_id,sh.stock_price,sh.stock_qty')
            ->join('product p', 'p.product_id=sh.product_id')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where('sh.deleteAt', NULL)
            ->where('sh.stock_qty !=', 0)
            ->get('stock_ho sh')
            ->result_array();

        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    public function witel_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RSTOCKWITEL']);
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
        $return = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,sh.stock_id,sh.stock_price,sh.stock_qty,sh.witel_id,sh.do_id')
            ->join('product p', 'p.product_id=sh.product_id')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where('sh.deleteAt', NULL)
            ->where('sh.witel_id', $witel_id)
            ->where('sh.stock_qty !=', 0)
            ->get('stock_witel sh')
            ->result_array();

        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }


    public function allByWitel_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RSTOCKBYWITEL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }

        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->get_where('witel_user w', ['w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email']])->row_array();
        if ($check == NULL) {
            $res = formatResponse(400, [], [], 'You\'re not from this witel', [], '');
            $this->response($res, 400);
        }
        $return = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,sh.stock_id,sh.stock_price,sh.stock_qty,sh.witel_id,sh.do_id')
            ->join('product p', 'p.product_id=sh.product_id')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where('sh.deleteAt', NULL)
            ->where('sh.witel_id', $check['witel_id'])
            ->where('sh.stock_qty !=', 0)
            ->get('stock_witel sh')
            ->result_array();

        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }
}
