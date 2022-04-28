<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuthModel extends CI_Model
{
    public function cekEmailAndPassword(string $email, string $password)
    {
        $cek = $this->db->get_where('user', ['email' => $email, 'password' => md5($password), 'deleteAt' => NULL])->row_array();
        return $cek;
    }
}
