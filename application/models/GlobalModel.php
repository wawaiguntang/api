<?php

class GlobalModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get data
     * @param string $table nama tabel
     * @param array $where kondisi
     * @param bool $tipeResultArray hasil
     * @param string $order_by nama field
     * @param string $or asc atau desc
     */
    public function getData(string $table, array $where = [], bool $tipeResultArray = true, string $order_by = null, string $or = 'DESC')
    {
        if ($tipeResultArray == true) {
            if ($order_by == null) {
                $in = $this->db->get_where($table, $where)->result_array();
                return $in;
            } else {
                $in = $this->db->order_by($order_by, $or)->get_where($table, $where)->result_array();
                return $in;
            }
        } else {
            $in = $this->db->get_where($table, $where)->row_array();
            return $in;
        }
    }

    public function insert($table, $params)
    {
        $in = $this->db->insert($table, $params);
        return $in;
    }

    public function update($table, $params, $where)
    {
        $this->db->where($where);
        $in = $this->db->update($table, $params);
        return $in;
    }

    public function delete($table, $where)
    {
        $this->db->where($where);
        $in = $this->db->update($table, ['deleteAt' => date('Y-m-d H:i:s')]);
        return $in;
    }
}
