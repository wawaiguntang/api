<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RoleModel extends CI_Model
{
    public function allRole(): array
    {
        $cek = $this->db->get_where('role', ['deleteAt' => NULL])->result_array();
        return $cek;
    }

    public function oneRole(string $roleCode): array
    {
        $cek = $this->db->get_where('role', ['roleCode' => $roleCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }

    public function addRole(array $params)
    {
        $in = $this->db->insert('role', $params);
        return $in;
    }

    public function editRole(array $params, array $where)
    {
        $up = $this->db->where($where)->update('role', $params);
        return $up;
    }

    public function deleteRole(string $roleCode)
    {
        $this->db->trans_begin();
        $this->db->where('roleCode', $roleCode)->update('role_permission', ['deleteAt' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->where('roleCode', $roleCode)->update('role_user', ['deleteAt' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->where('roleCode', $roleCode)->update('role', ['deleteAt' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function cekRolePermission(string $roleCode, string $permissionCode)
    {
        $where = [
            'permissionCode' => $permissionCode,
            'roleCode' => $roleCode,
            'deleteAt' => NULL
        ];
        $cek = $this->db->get_where('role_permission', $where)->row_array();
        return $cek;
    }

    public function oneRolePermission(string $rpCode)
    {
        $cek = $this->db->get_where('role_permission', ['rpCode' => $rpCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }

    public function addRolePermission(array $params)
    {
        $cek = $this->db->insert('role_permission', $params);
        return $cek;
    }

    public function deleteRolePermission(string $rpCode)
    {
        $up = $this->db->where('rpCode', $rpCode)->update('role_permission', ['deleteAt' => date('Y-m-d H:i:s')]);
        return $up;
    }

    public function allRolePermission(string $roleCode, string $moduleCode)
    {
        $getDataPermission =  $this->db
            ->select('rp.rpCode,p.permissionCode,p.permission,p.description')
            ->join('permission p', 'p.permissionCode=rp.permissionCode')
            ->where(['rp.deleteAt' => NULL, 'rp.roleCode' => $roleCode, 'p.moduleCode' => $moduleCode])
            ->get('role_permission rp')
            ->result_array();
        return $getDataPermission;
    }
}
