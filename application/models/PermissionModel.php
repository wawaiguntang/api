<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PermissionModel extends CI_Model
{
    public function allPermission()
    {
        $cek = $this->db->get_where('permission', ['deleteAt' => NULL])->result_array();
        return $cek;
    }

    public function onePermission(string $permissionCode)
    {
        $cek = $this->db->get_where('permission', ['permissionCode' => $permissionCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }

    public function permissionByModule(string $moduleCode)
    {
        $cek = $this->db->get_where('permission', ['moduleCode' => $moduleCode, 'deleteAt' => NULL])->result_array();
        return $cek;
    }
}
