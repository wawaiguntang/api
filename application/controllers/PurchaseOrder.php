<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class PurchaseOrder extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RPO', 'CPO', 'UPO', 'UCPO', 'DPO', 'RPOI', 'CPOI', 'UPOI', 'DPOI', 'USPOITP', 'USPOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = $this->db->where('deleteAt', NULL)->where_in('po_status', ['issued'])->get('po')->result_array();
        $res = formatResponse(200, $data, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RPO', 'CPO', 'UPO', 'UCPO', 'DPO', 'RPOI', 'CPOI', 'UPOI', 'DPOI', 'USPOITP', 'USPOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $po_id = $this->get('id');
        if ($po_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po', ['deleteAt' => NULL, 'po_id' => $po_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data po not found', [], '');
            $this->response($res, 404);
        }
        $return = $getData;
        $return['supplier'] = $this->GlobalModel->getData('supplier', ['supplier_id' => $getData['supplier_id']], false);
        $return['item'] = $this->db
            ->select('p.product_id,p.product_name,p.product_portion,b.brand_name,pi.item_price,pi.item_qty,pi.item_total')
            ->join('product p', 'p.product_id=pi.product_id')
            ->join('brand b', 'b.brand_id=p.brand_id')
            ->where('pi.deleteAt', NULL)
            ->get('po_item pi')
            ->result_array();
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CPO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'po_code' => $this->post('po_code'),
            'po_date' => $this->post('po_date'),
            'supplier_id' => $this->post('supplier_id'),
            'po_status' => 'issued',
        );

        $make = $this->validator->make($data, [
            'po_code' => 'required',
            'po_date' => 'required',
            'supplier_id' => 'required',
        ]);

        $make->setAliases([
            'po_code' => 'PO Code',
            'po_date' => 'PO Date',
            'supplier_id' => 'Supplier',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('supplier', ['supplier_id' => $data['supplier_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data supplier not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->insert('po', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('po', ['po_id' => $this->db->insert_id(), 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create po');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create po', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UPO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $po_id = $this->get('id');
        if ($po_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po', ['deleteAt' => NULL, 'po_id' => $po_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data po not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['po_status'] == 'processed' || $getData['po_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t edit po', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'po_code' => $this->put('po_code'),
            'po_date' => $this->put('po_date'),
            'supplier_id' => $this->put('supplier_id'),
        );

        $make = $this->validator->make($data, [
            'po_code' => 'required',
            'po_date' => 'required',
            'supplier_id' => 'required',
        ]);

        $make->setAliases([
            'po_code' => 'PO Code',
            'po_date' => 'PO Date',
            'supplier_id' => 'Supplier',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('supplier', ['supplier_id' => $data['supplier_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data supplier not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('po', $data, ['po_id' => $po_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('po', ['po_id' => $po_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create po');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create po', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DPO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $po_id = $this->get('id');
        if ($po_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->GlobalModel->getData('po', ['po_id' => $po_id, 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(400, [], [], 'Data PO not found', [], '');
            $this->response($res, 400);
        } else {
            if ($getData['po_status'] == 'processed' || $getData['po_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t delete po', [], '');
                $this->response($res, 400);
            }
        }

        $cek = $this->GlobalModel->delete('po_item', ['po_id' => $po_id]);
        $cek = $this->GlobalModel->delete('po', ['po_id' => $po_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete po');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to delete po', [], '');
            $this->response($res, 400);
        }
    }

    public function charge_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UCPO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'po_charge' => $this->post('po_charge'),
            'po_id' => $this->post('po_id'),
        );

        $make = $this->validator->make($data, [
            'po_charge' => 'required',
            'po_id' => 'required',
        ]);

        $make->setAliases([
            'po_charge' => 'Charge',
            'po_id' => 'PO Code',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('po', ['po_id' => $data['po_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data PO not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('po', $data, ['po_id' => $data['po_id'], 'deleteAt' => NULL]);
            if ($cek) {
                $this->updateDataPO($data['po_id']);
                $data = $this->GlobalModel->getData('po', ['po_id' => $getData['po_id'], 'deleteAt' => NULL], false);
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
        $permission = checkPermission($this->payload['data']['email'], ['RPO', 'CPO', 'UPO', 'UCPO', 'DPO', 'RPOI', 'CPOI', 'DPOI', 'UCPO', 'USPOITP', 'USPOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $po_id = $this->get('id');
        if ($po_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po', ['deleteAt' => NULL, 'po_id' => $po_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data po not found', [], '');
            $this->response($res, 404);
        }
        $getAllItem = $this->GlobalModel->getData('po_item', ['po_id' => $po_id, 'deleteAt' => NULL]);
        $res = formatResponse(200, $getAllItem, [], '', [], '');
        $this->response($res, 200);
    }

    public function oneItem_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RPO', 'CPO', 'UPO', 'UCPO', 'DPO', 'RPOI', 'CPOI', 'DPOI', 'UCPO', 'USPOITP', 'USPOPTD', 'RROPODO', 'RROPODOBW']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $item_id = $this->get('id');
        if ($item_id == NULL) {
            $res = formatResponse(400, [], [], 'ID item is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po_item', ['deleteAt' => NULL, 'item_id' => $item_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data item not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function addItem_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CPOI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'product_id' => $this->post('product_id'),
            'item_price' => $this->post('item_price'),
            'item_qty' => $this->post('item_qty'),
            'po_id' => $this->post('po_id'),
        );

        $make = $this->validator->make($data, [
            'product_id' => 'required',
            'item_price' => 'required|numeric',
            'item_qty' => 'required|numeric',
            'po_id' => 'required',
        ]);

        $make->setAliases([
            'product_id' => 'Product',
            'item_price' => 'Price',
            'item_qty' => 'Quantity',
            'po_id' => 'PO Code',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getDataProduct = $this->GlobalModel->getData('product', ['product_id' => $data['product_id'], 'deleteAt' => NULL], false);
            if ($getDataProduct == NULL) {
                $res = formatResponse(400, [], [], 'Data product not found', [], '');
                $this->response($res, 400);
            }
            $getData = $this->GlobalModel->getData('po', ['po_id' => $data['po_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data PO not found', [], '');
                $this->response($res, 400);
            } else {
                if ($getData['po_status'] == 'processed' || $getData['po_status'] == 'done') {
                    $res = formatResponse(400, [], [], 'Can\'t add item to po', [], '');
                    $this->response($res, 400);
                }
            }
            $data['item_total'] = $data['item_price'] * $data['item_qty'];
            $cek = $this->GlobalModel->insert('po_item', $data);
            $item_id = $this->db->insert_id();
            if ($cek) {
                $this->updateDataPO($data['po_id']);
                $data = $this->GlobalModel->getData('po_item', ['item_id' => $item_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create item po');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create item po', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function editItem_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UPOI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $item_id = $this->get('id');
        if ($item_id == NULL) {
            $res = formatResponse(400, [], [], 'ID item is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po_item', ['deleteAt' => NULL, 'item_id' => $item_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data item not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'product_id' => $this->put('product_id'),
            'item_price' => $this->put('item_price'),
            'item_qty' => $this->put('item_qty'),
        );

        $make = $this->validator->make($data, [
            'product_id' => 'required',
            'item_price' => 'required|numeric',
            'item_qty' => 'required|numeric',
        ]);

        $make->setAliases([
            'product_id' => 'Product',
            'item_price' => 'Price',
            'item_qty' => 'Quantity',
        ]);

        $make->validate();

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('po', ['po_id' => $getData['po_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data PO not found', [], '');
                $this->response($res, 400);
            } else {
                if ($getData['po_status'] == 'processed' || $getData['po_status'] == 'done') {
                    $res = formatResponse(400, [], [], 'Can\'t edit item po', [], '');
                    $this->response($res, 400);
                }
            }
            $getDataProduct = $this->GlobalModel->getData('product', ['product_id' => $data['product_id'], 'deleteAt' => NULL], false);
            if ($getDataProduct == NULL) {
                $res = formatResponse(400, [], [], 'Data product not found', [], '');
                $this->response($res, 400);
            }
            $data['item_total'] = $data['item_price'] * $data['item_qty'];
            $cek = $this->GlobalModel->update('po_item', $data, ['item_id' => $item_id]);
            if ($cek) {
                $this->updateDataPO($getData['po_id']);
                $data = $this->GlobalModel->getData('po_item', ['item_id' => $item_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update item po');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update item po', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteItem_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DPOI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $item_id = $this->get('id');
        if ($item_id == NULL) {
            $res = formatResponse(400, [], [], 'ID item is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po_item', ['deleteAt' => NULL, 'item_id' => $item_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data item not found', [], '');
            $this->response($res, 404);
        }

        $getData = $this->GlobalModel->getData('po', ['po_id' => $getData['po_id'], 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(400, [], [], 'Data PO not found', [], '');
            $this->response($res, 400);
        } else {
            if ($getData['po_status'] == 'processed' || $getData['po_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t delete item po', [], '');
                $this->response($res, 400);
            }
        }

        $cek = $this->GlobalModel->delete('po_item', ['item_id' => $item_id]);
        if ($cek) {
            $this->updateDataPO($getData['po_id']);
            $res = formatResponse(200, [], [], '', [], 'Success to delete item po');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to delete item po', [], '');
            $this->response($res, 400);
        }
    }

    private function updateDataPO(string $po_id)
    {
        $data = $this->GlobalModel->getData('po', ['po_id' => $po_id, 'deleteAt' => NULL], false);
        $getAllItem = $this->GlobalModel->getData('po_item', ['po_id' => $po_id, 'deleteAt' => NULL]);
        $subTotal = 0;
        foreach ($getAllItem as $k => $v) {
            $item_total = $v['item_price'] * $v['item_qty'];
            $subTotal += $item_total;
        }
        $grandTotal = $subTotal + $data['po_charge'];

        $this->GlobalModel->update('po', [
            'po_subtotal' => $subTotal,
            'po_grandtotal' => $grandTotal
        ], ['po_id' => $po_id, 'deleteAt' => NULL]);
    }

    public function issuedToProcessed_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['USPOITP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $po_id = $this->get('id');
        if ($po_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po', ['deleteAt' => NULL, 'po_id' => $po_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data po not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['po_status'] == 'processed' || $getData['po_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t edit po', [], '');
                $this->response($res, 400);
            }
        }
        $getDataItem = $this->GlobalModel->getData('po_item', ['deleteAt' => NULL, 'po_id' => $po_id], false);
        if ($getDataItem == NULL) {
            $res = formatResponse(404, [], [], 'Data item po not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->update('po', ['po_status' => 'processed'], ['po_id' => $po_id]);
        if ($cek) {
            $data = $this->GlobalModel->getData('po', ['po_id' => $po_id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], 'Success to change status po from issued to processed');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to change status po from issued to processed', [], '');
            $this->response($res, 400);
        }
    }

    public function processedToDone_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['USPOPTD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $po_id = $this->get('id');
        if ($po_id == NULL) {
            $res = formatResponse(400, [], [], 'ID po is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('po', ['deleteAt' => NULL, 'po_id' => $po_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data po not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['po_status'] == 'issued' || $getData['po_status'] == 'done') {
                $res = formatResponse(400, [], [], 'Can\'t edit po', [], '');
                $this->response($res, 400);
            }
        }
        $getDataItem = $this->GlobalModel->getData('po_item', ['deleteAt' => NULL, 'po_id' => $po_id], false);
        if ($getDataItem == NULL) {
            $res = formatResponse(404, [], [], 'Data item po not found', [], '');
            $this->response($res, 404);
        }
        if ($this->updateDataStock($po_id)) {
            $data = $this->GlobalModel->getData('po', ['po_id' => $po_id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], 'Success to change status po from issued to processed');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to change status po from issued to processed', [], '');
            $this->response($res, 400);
        }
    }

    private function updateDataStock(string $po_id)
    {
        $getAllItem = $this->GlobalModel->getData('po_item', ['po_id' => $po_id, 'deleteAt' => NULL]);
        $this->db->trans_begin();
        foreach ($getAllItem as $k => $v) {
            $check = $this->GlobalModel->getData('stock_ho', ['product_id' => $v['product_id'], 'stock_price' => $v['item_price']], false);
            if ($check == NULL) {
                $in = $this->GlobalModel->insert('stock_ho', ['product_id' => $v['product_id'], 'stock_price' => $v['item_price'], 'stock_qty' => $v['item_qty']]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return false;
                }
            } else {
                $up = $this->GlobalModel->update('stock_ho', ['stock_qty' => $v['item_qty']], ['product_id' => $v['product_id'], 'stock_price' => $v['item_price']]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return false;
                }
            }
        }
        $cek = $this->GlobalModel->update('po', ['po_status' => 'done', 'ro_date' => date('Y-m-d H:i:s')], ['po_id' => $po_id]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
}
