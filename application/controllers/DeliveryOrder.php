<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class DeliveryOrder extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RDO', 'CDO', 'UDO', 'DDO', 'UCDO', 'RDOI', 'CDOI', 'UDOI', 'DDOI', 'USDOITP', 'USDOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = $this->db->where('deleteAt', NULL)->where_in('do_status', ['issued'])->get('do')->result_array();
        $res = formatResponse(200, $data, [], '', [], '');
        $this->response($res, 200);
    }

    public function allByWitel_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RDOBW', 'CDO', 'UDO', 'DDO', 'UCDO', 'RDOI', 'CDOI', 'UDOI', 'DDOI', 'USDOITP', 'USDOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $witel_id = $this->get('id');
        if ($witel_id == NULL) {
            $res = formatResponse(400, [], [], 'ID witel is required', [], '');
            $this->response($res, 400);
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->get_where('witel_user w', ['w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email'], 'w.witel_id' => $witel_id])->row_array();
        if ($check == NULL) {
            $res = formatResponse(400, [], [], 'You\'re not from this witel', [], '');
            $this->response($res, 400);
        }
        $data = $this->db->where('deleteAt', NULL)->where_in('do_status', ['issued'])->where('witel_id', $witel_id)->get('do')->result_array();
        $res = formatResponse(200, $data, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RDOBW', 'RDO', 'CDO', 'UDO', 'DDO', 'UCDO', 'RDOI', 'CDOI', 'UDOI', 'DDOI', 'USDOITP', 'USDOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $do_id = $this->get('id');
        if ($do_id == NULL) {
            $res = formatResponse(400, [], [], 'ID do is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('do', ['deleteAt' => NULL, 'do_id' => $do_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data do not found', [], '');
            $this->response($res, 404);
        }
        $return = $getData;
        $return['witel'] = $this->db
            ->select('p.witel_id,p.witel_name,p.witel_code,b.region_name,p.createAt,p.updateAt,p.deleteAt')
            ->join('region b', 'b.region_id=p.region_id')
            ->where(['p.witel_id' => $getData['witel_id']])
            ->get('witel p')
            ->row_array();
        $return['witel']['item'] = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,pi.item_price,pi.item_qty,pi.item_total')
            ->join('stock_ho s', 's.stock_id_id=pi.stock_id')
            ->join('product p', 'p.product_id=s.product_id')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where('pi.deleteAt', NULL)
            ->get('do_item pi')
            ->result_array();
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CDO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'do_code' => $this->post('do_code'),
            'do_date' => $this->post('do_date'),
            'witel_id' => $this->post('witel_id'),
            'do_status' => 'issued',
        );

        $make = $this->validator->make($data, [
            'do_code' => 'required',
            'do_date' => 'required',
            'witel_id' => 'required',
        ]);

        $make->setAliases([
            'do_code' => 'DO Code',
            'do_date' => 'DO Date',
            'witel_id' => 'Witel',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('witel', ['witel_id' => $data['witel_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data witel not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->insert('do', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('do', ['do_id' => $this->db->insert_id(), 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create do');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create do', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UDO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $do_id = $this->get('id');
        if ($do_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('do', ['deleteAt' => NULL, 'do_id' => $do_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data do not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['do_status'] == 'processed' || $getData['do_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t edit po', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'do_code' => $this->put('do_code'),
            'do_date' => $this->put('do_date'),
            'witel_id' => $this->put('witel_id'),
        );

        $make = $this->validator->make($data, [
            'do_code' => 'required',
            'do_date' => 'required',
            'witel_id' => 'required',
        ]);

        $make->setAliases([
            'do_code' => 'DO Code',
            'do_date' => 'DO Date',
            'witel_id' => 'Witel',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('witel', ['witel_id' => $data['witel_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data witel not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('do', $data, ['do_id' => $do_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('do', ['do_id' => $do_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to edit do');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to edit do', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DDO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $do_id = $this->get('id');
        if ($do_id == NULL) {
            $res = formatResponse(400, [], [], 'ID do is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->GlobalModel->getData('do', ['do_id' => $do_id, 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(400, [], [], 'Data DO not found', [], '');
            $this->response($res, 400);
        } else {
            if ($getData['do_status'] == 'processed' || $getData['do_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t delete po', [], '');
                $this->response($res, 400);
            }
        }

        $cek = $this->GlobalModel->delete('do_item', ['do_id' => $do_id]);
        $cek = $this->GlobalModel->delete('do', ['do_id' => $do_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete do');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to delete do', [], '');
            $this->response($res, 400);
        }
    }

    public function charge_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UCDO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'do_charge' => $this->post('do_charge'),
            'do_id' => $this->post('do_id'),
        );

        $make = $this->validator->make($data, [
            'do_charge' => 'required|numeric',
            'do_id' => 'required',
        ]);

        $make->setAliases([
            'do_charge' => 'Charge',
            'do_id' => 'DO Code',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('do', ['do_id' => $data['do_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data DO not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('do', $data, ['do_id' => $data['do_id'], 'deleteAt' => NULL]);
            if ($cek) {
                $this->updateDataDO($data['do_id']);
                $data = $this->GlobalModel->getData('do', ['do_id' => $data['do_id'], 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success add charge');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed add charge', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function allItem_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RDOBW', 'RDO', 'CDO', 'UDO', 'DDO', 'UCDO', 'RDOI', 'CDOI', 'UDOI', 'DDOI', 'UCDO', 'USDOITP', 'USDOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $do_id = $this->get('id');
        if ($do_id == NULL) {
            $res = formatResponse(400, [], [], 'ID do is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('do', ['deleteAt' => NULL, 'do_id' => $do_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data do not found', [], '');
            $this->response($res, 404);
        }
        $return = [];
        $getAllItem = $this->GlobalModel->getData('do_item', ['do_id' => $do_id, 'deleteAt' => NULL]);
        foreach ($getAllItem as $k => $v) {
            $product = $this->db
                ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,sh.stock_id,sh.stock_price,sh.stock_qty')
                ->join('product p', 'p.product_id=sh.product_id')
                ->join('brand b', 'b.brand_id=p.brand_id')
                ->where('sh.stock_id', $v['stock_id'])
                ->where('sh.deleteAt', NULL)
                ->get('stock_ho sh')
                ->row_array();
            $v['product'] = $product;
            $return[] = $v;
        }
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    public function oneItem_get()
    {
        $permission = checkPermission($this->payload['data']['email'],  ['RDOBW', 'RDO', 'CDO', 'UDO', 'DDO', 'UCDO', 'RDOI', 'CDOI', 'UDOI', 'DDOI', 'UCDO', 'USDOITP', 'USDOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $item_id = $this->get('id');
        if ($item_id == NULL) {
            $res = formatResponse(400, [], [], 'ID item is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'item_id' => $item_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data item not found', [], '');
            $this->response($res, 404);
        }
        $getData['product'] = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,sh.stock_id,sh.stock_price,sh.stock_qty')
            ->join('product p', 'p.product_id=sh.product_id')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where('sh.stock_id', $getData['stock_id'])
            ->where('sh.deleteAt', NULL)
            ->get('stock_ho sh')
            ->row_array();
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function addItem_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CDOI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'stock_id' => $this->post('stock_id'),
            'item_qty' => $this->post('item_qty'),
            'do_id' => $this->post('do_id'),
        );

        $make = $this->validator->make($data, [
            'stock_id' => 'required',
            'item_qty' => 'required|numeric',
            'do_id' => 'required',
        ]);

        $make->setAliases([
            'stock_id' => 'Product',
            'item_qty' => 'Quantity',
            'do_id' => 'DO Code',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('stock_ho', ['stock_id' => $data['stock_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data stock not found', [], '');
                $this->response($res, 400);
            }
            $getDataDO = $this->GlobalModel->getData('do', ['do_id' => $data['do_id'], 'deleteAt' => NULL], false);
            if ($getDataDO == NULL) {
                $res = formatResponse(400, [], [], 'Data DO not found', [], '');
                $this->response($res, 400);
            } else {
                if ($getDataDO['do_status'] == 'processed' || $getDataDO['do_status'] == 'done') {
                    $res = formatResponse(400, [], [], 'Can\'t add item to do', [], '');
                    $this->response($res, 400);
                }
            }
            $data['item_price'] = $getData['stock_price'];
            $data['item_total'] = $getData['stock_price'] * $data['item_qty'];
            if ($this->GlobalModel->insert('do_item', $data)) {
                $data = $this->GlobalModel->getData('do_item', ['item_id' => $this->db->insert_id(), 'deleteAt' => NULL], false);
                $this->updateDataDO($data['do_id']);
                $res = formatResponse(200, $data, [], '', [], 'Success to create item do');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create item do', [], '');
                $this->response($res, 400);
            }
        }
    }


    public function editItem_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UDOI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $item_id = $this->get('id');
        if ($item_id == NULL) {
            $res = formatResponse(400, [], [], 'ID item is required', [], '');
            $this->response($res, 400);
        }
        $getDataItem = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'item_id' => $item_id], false);
        if ($getDataItem == NULL) {
            $res = formatResponse(404, [], [], 'Data item not found', [], '');
            $this->response($res, 404);
        }
        $data = array(
            'stock_id' => $this->put('stock_id'),
            'item_qty' => $this->put('item_qty'),
        );

        $make = $this->validator->make($data, [
            'stock_id' => 'required',
            'item_qty' => 'required|numeric',
        ]);

        $make->setAliases([
            'stock_id' => 'Product',
            'item_qty' => 'Quantity',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getDataDO = $this->GlobalModel->getData('do', ['do_id' => $getDataItem['do_id'], 'deleteAt' => NULL], false);
            if ($getDataDO == NULL) {
                $res = formatResponse(400, [], [], 'Data DO not found', [], '');
                $this->response($res, 400);
            } else {
                if ($getDataDO['do_status'] == 'processed' || $getDataDO['do_status'] == 'done') {
                    $res = formatResponse(400, [], [], 'Can\'t edit item do', [], '');
                    $this->response($res, 400);
                }
            }
            $getDataStock = $this->GlobalModel->getData('stock_ho', ['stock_id' => $data['stock_id'], 'deleteAt' => NULL], false);
            if ($getDataStock == NULL) {
                $res = formatResponse(400, [], [], 'Data stock not found', [], '');
                $this->response($res, 400);
            }
            $data['item_price'] = $getDataStock['stock_price'];
            $data['item_total'] = $data['item_price'] * $data['item_qty'];
            $cek = $this->GlobalModel->update('do_item', $data, ['item_id' => $item_id]);
            if ($cek) {
                $this->updateDataDO($getDataItem['do_id']);
                $data = $this->GlobalModel->getData('do_item', ['item_id' => $item_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update item do');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update item do', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteItem_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DDOI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $item_id = $this->get('id');
        if ($item_id == NULL) {
            $res = formatResponse(400, [], [], 'ID item is required', [], '');
            $this->response($res, 400);
        }
        $getDataItem = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'item_id' => $item_id], false);
        if ($getDataItem == NULL) {
            $res = formatResponse(404, [], [], 'Data item not found', [], '');
            $this->response($res, 404);
        }

        $getDataDO = $this->GlobalModel->getData('do', ['do_id' => $getDataItem['do_id'], 'deleteAt' => NULL], false);
        if ($getDataDO == NULL) {
            $res = formatResponse(400, [], [], 'Data DO not found', [], '');
            $this->response($res, 400);
        } else {
            if ($getDataDO['do_status'] == 'processed' || $getDataDO['do_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t delete item do', [], '');
                $this->response($res, 400);
            }
        }

        $cek = $this->GlobalModel->delete('do_item', ['item_id' => $item_id]);
        if ($cek) {
            $this->updateDataDO($getDataItem['do_id']);
            $res = formatResponse(200, [], [], '', [], 'Success to delete item do');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to delete item do', [], '');
            $this->response($res, 400);
        }
    }

    public function issuedToProcessed_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['USDOITP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $do_id = $this->get('id');
        if ($do_id == NULL) {
            $res = formatResponse(400, [], [], 'ID do is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('do', ['deleteAt' => NULL, 'do_id' => $do_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data do not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['do_status'] == 'processed' || $getData['do_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t edit do', [], '');
                $this->response($res, 400);
            }
        }
        $getAllItem = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'do_id' => $do_id], false);
        if ($getAllItem == NULL) {
            $res = formatResponse(404, [], [], 'Data item do not found', [], '');
            $this->response($res, 404);
        }


        $cek = $this->updateStock($do_id);
        if ($cek['status']) {
            $data = $this->GlobalModel->getData('do', ['do_id' => $do_id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], $cek['message']);
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], $cek['message'], [], '');
            $this->response($res, 400);
        }
    }

    public function processedToDone_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['USDOPTD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $do_id = $this->get('id');
        if ($do_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('do', ['deleteAt' => NULL, 'do_id' => $do_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data do not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['do_status'] == 'issued' || $getData['do_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t edit po', [], '');
                $this->response($res, 400);
            }
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->get_where('witel_user w', ['w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email'], 'w.witel_id' => $getData['witel_id']])->row_array();
        if ($check == NULL) {
            $res = formatResponse(400, [], [], 'You\'re not from this witel', [], '');
            $this->response($res, 400);
        }
        $getAllItem = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'do_id' => $do_id], false);
        if ($getAllItem == NULL) {
            $res = formatResponse(404, [], [], 'Data item do not found', [], '');
            $this->response($res, 404);
        }

        if ($this->insertStock($do_id)) {
            $data = $this->GlobalModel->getData('do', ['do_id' => $do_id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], 'Delivery order success change status to done');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Delivery order failed change status to done', [], '');
            $this->response($res, 400);
        }
    }

    private function insertStock(string $do_id)
    {
        $this->db->trans_begin();
        $getDO = $this->GlobalModel->getData('do', ['deleteAt' => NULL, 'do_id' => $do_id],false);
        $getAllItem = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'do_id' => $do_id]);
        $data = [];
        foreach ($getAllItem as $k => $v) {
            $getStock = $this->GlobalModel->getData('stock_ho', ['stock_id' => $v['stock_id']], false);
            $data[] = [
                'product_id' => $getStock['product_id'],
                'stock_price' => $v['item_price'],
                'stock_qty' => $v['item_qty'],
                'witel_id' => $getDO['witel_id'],
                'do_id' => $getDO['do_id']
            ];
        }
        $this->db->insert_batch('stock_witel', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->GlobalModel->update('do', ['do_status' => 'done', 'ro_date' => date('Y-m-d H:i:s')], ['do_id' => $do_id]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    private function updateStock(string $do_id)
    {
        $this->db->trans_begin();
        $getAllItem = $this->GlobalModel->getData('do_item', ['deleteAt' => NULL, 'do_id' => $do_id]);
        foreach ($getAllItem as $k => $v) {
            $getStock = $this->GlobalModel->getData('stock_ho', ['deleteAt' => NULL, 'stock_id' => $v['stock_id']], false);
            if (($getStock['stock_qty'] - $v['item_qty']) < 0) {
                $this->db->trans_rollback();
                return [
                    'status' => false,
                    'message' => 'Current stock is not sufficient'
                ];
            } else {
                $dataStock = [
                    'stock_qty' => $getStock['stock_qty'] - $v['item_qty']
                ];
                $this->GlobalModel->update('stock_ho', $dataStock, ['stock_id' => $v['stock_id']]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return [
                        'status' => false,
                        'message' => 'Failed to move stock'
                    ];
                }
            }
        }
        $this->GlobalModel->update('do', ['do_status' => 'processed'], ['do_id' => $do_id]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Failed change status'
            ];
        }
        $this->db->trans_commit();
        return [
            'status' => true,
            'message' => 'Delivery order processed'
        ];
    }

    private function updateDataDO(string $do_id)
    {
        $this->db->trans_begin();
        $data = $this->GlobalModel->getData('do', ['do_id' => $do_id, 'deleteAt' => NULL], false);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $getAllItem = $this->GlobalModel->getData('do_item', ['do_id' => $do_id, 'deleteAt' => NULL]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $subTotal = 0;
        foreach ($getAllItem as $k => $v) {
            $item_total = $v['item_price'] * $v['item_qty'];
            $subTotal += $item_total;
        }
        $grandTotal = $subTotal + $data['do_charge'];

        $this->GlobalModel->update('do', [
            'do_subtotal' => $subTotal,
            'do_grandtotal' => $grandTotal
        ], ['do_id' => $do_id, 'deleteAt' => NULL]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}
