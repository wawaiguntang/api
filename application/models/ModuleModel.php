<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ModuleModel extends CI_Model
{
    public function allModule()
    {
        $cek = $this->db->get_where('module', ['deleteAt' => NULL])->result_array();
        return $cek;
    }

    public function oneModule(string $moduleCode)
    {
        $cek = $this->db->get_where('module', ['moduleCode' => $moduleCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }
}
