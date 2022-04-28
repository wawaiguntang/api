<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserModel extends CI_Model
{
    public function allUser(): array
    {
        $cek = $this->db->select('userCode,name,email,nik_ta,nik_api,package_id,photo,createAt,updateAt,deleteAt')->get_where('user', ['deleteAt' => NULL])->result_array();
        return $cek;
    }

    public function oneUser(string $userCode)
    {
        $cek = $this->db->select('userCode,name,email,nik_ta,nik_api,package_id,photo,createAt,updateAt,deleteAt')->get_where('user', ['userCode' => $userCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }

    public function addUser(array $params)
    {
        $in = $this->db->insert('user', $params);
        return $in;
    }

    public function editUser(array $params, array $where)
    {
        $up = $this->db->where($where)->update('user', $params);
        return $up;
    }

    public function deleteUser(string $userCode)
    {
        $this->db->trans_begin();
        $this->db->where('userCode', $userCode)->update('user', ['deleteAt' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->where('userCode', $userCode)->update('user_permission', ['deleteAt' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->where('userCode', $userCode)->update('role_user', ['deleteAt' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function getRoleUser(string $userCode)
    {
        $get = $this->db
            ->select('r.roleCode, r.role, r.type')
            ->join('role r', 'r.roleCode=ru.roleCode')
            ->join('user u', 'u.userCode=ru.userCode')
            ->get_where('role_user ru', ['u.userCode' => $userCode, 'u.deleteAt' => NULL])
            ->result_array();
        return $get;
    }

    public function uniqueEmail(string $email)
    {
        $cek = $this->db->get_where('user', ['email' => $email])->row_array();
        return $cek;
    }

    public function cekUserRole(string $userCode, string $roleCode)
    {
        $where = [
            'userCode' => $userCode,
            'roleCode' => $roleCode,
            'deleteAt' => NULL
        ];
        $cek = $this->db->get_where('role_user', $where)->row_array();
        return $cek;
    }

    public function allRoleByEmail(string $email)
    {
        $get = $this->db
            ->join('user u', 'u.userCode=ru.userCode')
            ->where(['u.email' => $email, 'ru.deleteAt' => NULL])
            ->get('role_user ru')->result_array();
        return $get;
    }

    public function oneUserRole(string $ruCode)
    {
        $cek = $this->db->get_where('role_user', ['ruCode' => $ruCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }

    public function addUserRole(array $params)
    {
        $cek = $this->db->insert('role_user', $params);
        return $cek;
    }

    public function deleteUserRole(string $ruCode)
    {
        $up = $this->db->where('ruCode', $ruCode)->update('role_user', ['deleteAt' => date('Y-m-d H:i:s')]);
        return $up;
    }

    public function cekUserPermission(string $userCode, string $permissionCode)
    {
        $where = [
            'permissionCode' => $permissionCode,
            'userCode' => $userCode,
            'deleteAt' => NULL
        ];
        $cek = $this->db->get_where('user_permission', $where)->row_array();
        return $cek;
    }

    public function oneUserPermission(string $upCode): array
    {
        $cek = $this->db->get_where('user_permission', ['upCode' => $upCode, 'deleteAt' => NULL])->row_array();
        return $cek;
    }

    public function addUserPermission(array $params)
    {
        $cek = $this->db->insert('user_permission', $params);
        return $cek;
    }

    public function deleteUserPermission(string $upCode)
    {
        $up = $this->db->where('upCode', $upCode)->update('user_permission', ['deleteAt' => date('Y-m-d H:i:s')]);
        return $up;
    }

    public function allUserPermission(string $userCode)
    {
        $where = [
            'up.userCode' => $userCode,
            'up.deleteAt' => NULL
        ];
        $get = $this->db
            ->select('up.upCode,p.permissionCode,p.permission,p.description')
            ->join('permission p', 'p.permissionCode=up.permissionCode')
            ->get_where('user_permission up', $where)->result_array();
        return $get;
    }
}
