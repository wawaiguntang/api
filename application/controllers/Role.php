<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Role extends RestController
{
	private $validator;
	private $payload;
	public function __construct()
	{
		parent::__construct();
		$this->validator = new Validator();
		$this->load->model('RoleModel');
		$this->load->model('ModuleModel');
		$this->load->model('PermissionModel');
		$this->payload = JWT_Verif_Access();
		if ($this->payload['status'] == false) {
			$res = formatResponse(400, [], [], $this->payload['message'], [], '');
			$this->response($res, 400);
		}
	}
	public function all_get()
	{
		$permission = checkPermission($this->payload['data']['email'], ['RR', 'CRP', 'DRP', 'CRU', 'DRU', 'DR', 'UR']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$getData = $this->RoleModel->allRole();
		$res = formatResponse(200, $getData, [], '', [], '');
		$this->response($res, 200);
	}

	public function one_get()
	{
		$permission = checkPermission($this->payload['data']['email'], ['RR', 'CRP', 'DRP', 'CRU', 'DRU', 'DR', 'UR']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$roleCode = $this->get('id');
		if ($roleCode == NULL) {
			$res = formatResponse(400, [], [], 'ID role is required', [], '');
			$this->response($res, 400);
		}

		$getData = $this->RoleModel->oneRole($roleCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data not found', [], '');
			$this->response($res, 404);
		}
		$res = formatResponse(200, $getData, [], '', [], '');
		$this->response($res, 200);
	}

	public function add_post()
	{
		$data = array(
			'role' => $this->post('role'),
		);

		$make = $this->validator->make($data, [
			'role' => 'required',
		]);

		$make->setAliases([
			'role' => 'Role',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {
			$permission = checkPermission($this->payload['data']['email'], ['CR']);
			if ($permission['status'] == false) {
				$this->response($permission['data'], 400);
			}
			$cek = $this->RoleModel->addRole($data);
			if ($cek) {
				$data = $this->RoleModel->oneRole($this->db->insert_id());
				$res = formatResponse(200, $data, [], '', [], 'Success to create role');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed to create role', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function edit_put()
	{
		$permission = checkPermission($this->payload['data']['email'], ['UR']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$roleCode = $this->get('id');
		if ($roleCode == NULL) {
			$res = formatResponse(400, [], [], 'ID role is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->RoleModel->oneRole($roleCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data not found', [], '');
			$this->response($res, 404);
		} else {
			if ($getData['type'] == 'Master') {
				$res = formatResponse(400, [], [], 'This role cannot be edit', [], '');
				$this->response($res, 400);
			}
		}

		$data = array(
			'role' => $this->put('role'),
		);

		$make = $this->validator->make($data, [
			'role' => 'required',
		]);

		$make->setAliases([
			'role' => 'Role',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {

			$cek = $this->RoleModel->editRole($data, ['roleCode' => $roleCode]);
			if ($cek) {
				$data = $this->RoleModel->oneRole($roleCode);
				$res = formatResponse(200, $data, [], '', [], 'Success to update role');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed to update role', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function delete_delete()
	{
		$permission = checkPermission($this->payload['data']['email'], ['DR']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$roleCode = $this->get('id');
		if ($roleCode == NULL) {
			$res = formatResponse(400, [], [], 'ID role is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->RoleModel->oneRole($roleCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data not found', [], '');
			$this->response($res, 404);
		} else {
			if ($getData['type'] == 'Master') {
				$res = formatResponse(400, [], [], 'This role cannot be deleted', [], '');
				$this->response($res, 400);
			}
		}
		$cek = $this->RoleModel->deleteRole($roleCode);
		if ($cek) {
			$res = formatResponse(200, [], [], '', [], 'Success to delete role');
			$this->response($res, 200);
		} else {
			$res = formatResponse(200, [], [], 'Failed to delete role', [], '');
			$this->response($res, 400);
		}
	}

	public function addPermission_post()
	{
		$permission = checkPermission($this->payload['data']['email'], ['CRP']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$data = array(
			'permissionCode' => $this->post('permissionCode'),
			'roleCode' => $this->post('roleCode'),
		);

		$make = $this->validator->make($data, [
			'permissionCode' => 'required',
			'roleCode' => 'required',
		]);

		$make->setAliases([
			'permissionCode' => 'Permission',
			'roleCode' => 'Role',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {
			$getDataPermission = $this->PermissionModel->onePermission($this->post('permissionCode'));
			if ($getDataPermission == NULL) {
				$res = formatResponse(400, [], [], 'Data permission not found', [], '');
				$this->response($res, 404);
			}
			$getDataRole = $this->RoleModel->oneRole($this->post('roleCode'));
			if ($getDataRole == NULL) {
				$res = formatResponse(400, [], [], 'Data role not found', [], '');
				$this->response($res, 404);
			} else {
				if ($getDataRole['type'] == 'Master') {
					$res = formatResponse(400, [], [], 'This role cannot be add', [], '');
					$this->response($res, 400);
				}
			}
			$checkRolePermission =  $this->RoleModel->cekRolePermission($getDataRole['roleCode'], $getDataPermission['permissionCode']);
			if ($checkRolePermission != NULL) {
				$res = formatResponse(400, [], [], 'Data is already exist', [], '');
				$this->response($res, 400);
			}

			$cek = $this->RoleModel->addRolePermission($data);
			if ($cek) {
				$res = formatResponse(200, [], [], '', [], 'Success add permission to role');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed add permission to role', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function deletePermission_delete()
	{
		$permission = checkPermission($this->payload['data']['email'], ['DRP']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$rpCode = $this->get('id');
		if ($rpCode == NULL) {
			$res = formatResponse(400, [], [], 'ID role permission is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->RoleModel->oneRolePermission($rpCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data role permission not found', [], '');
			$this->response($res, 404);
		}
		$getDataPermission = $this->PermissionModel->onePermission($getData['permissionCode']);
		if ($getDataPermission == NULL) {
			$res = formatResponse(404, [], [], 'Data permission not found', [], '');
			$this->response($res, 404);
		}
		$getDataRole = $this->RoleModel->oneRole($getData['roleCode']);
		if ($getDataRole == NULL) {
			$res = formatResponse(404, [], [], 'Data role not found', [], '');
			$this->response($res, 404);
		} else {
			if ($getDataRole['type'] == 'Master') {
				$res = formatResponse(400, [], [], 'This role permission cannot be delete', [], '');
				$this->response($res, 400);
			} else {
				$cek = $this->RoleModel->deleteRolePermission($rpCode);
				if ($cek) {
					$res = formatResponse(200, [], [], '', [], 'Success delete permission from role');
					$this->response($res, 200);
				} else {
					$res = formatResponse(400, [], [], 'Failed delete permission from role', [], '');
					$this->response($res, 400);
				}
			}
		}
	}

	public function allPermission_get()
	{
		$permission = checkPermission($this->payload['data']['email'], ['RRP']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$roleCode = $this->get('id');
		if ($roleCode == NULL) {
			$res = formatResponse(400, [], [], 'ID role is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->RoleModel->oneRole($roleCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data role not found', [], '');
			$this->response($res, 404);
		}

		$module = $this->ModuleModel->allModule();
		$gg = [
			'roleCode' => $getData['roleCode'],
			'role' => $getData['role']
		];
		$temp = $gg;
		foreach ($module as $m => $c) {
			$permission = $this->RoleModel->allRolePermission($getData['roleCode'], $c['moduleCode']);
			if ($permission != NULL) {
				$rr = [
					'moduleCode' => $c['moduleCode'],
					'module' => $c['module'],
					'permission' => $permission
				];
				$temp['module'][] = $rr;
			}
		}
		$return[] = $temp;
		$res = formatResponse(200, $return, [], '', [], '');
		$this->response($res, 200);
	}
}
