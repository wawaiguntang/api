<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Test extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
    }

    public function testUpdateStock_get()
    {
        var_dump($this->updateDataStock(1));
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
                $up = $this->GlobalModel->update('stock_ho', ['stock_qty' => $v['item_qty'] + $check['stock_qty']], ['product_id' => $v['product_id'], 'stock_price' => $v['item_price']]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return false;
                }
            }
        }
        $this->db->trans_commit();
        return true;
    }
}
