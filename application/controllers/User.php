<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class User extends RestController
{
	private $validator;
	private $payload;
	public function __construct()
	{
		parent::__construct();
		$this->validator = new Validator();
		$this->load->model('UserModel');
		$this->load->model('RoleModel');
		$this->load->model('ModuleModel');
		$this->load->model('PermissionModel');
		$this->payload = JWT_Verif_Access();
		if ($this->payload['status'] == false) {
			$res = [
				'status' => 400,
				'data' => [],
				'error' => [
					'form' => [],
					'message' => $this->payload['message']
				],
				'success' => [
					'form' => [],
					'message' => ''
				]
			];
			$this->response($res, 400);
		}
	}
	public function all_get()
	{
		$permission = checkPermission($this->payload['data']['email'], ['RPRO', 'CPRO', 'UPRO', 'DPRO', 'APRO', 'DEPRO', 'CTEC', 'UTEC', 'CUSI', 'CFED', 'UFED', 'DFED', 'CDIS', 'UDIS', 'DDIS', 'CFLS', 'DFLS', 'CKHSL', 'UKHSL', 'DKHSL', 'CTKHS', 'CMKHS', 'CSAI', 'CFLI', 'DFLI', 'CSAT', 'CFLT', 'DFLT', 'CSV3', 'CDSV3', 'CSL', 'CFLL', 'DFLL', 'CSV4', 'CDV4', 'CSTD', 'URECON', 'CSTP', 'CPTS', 'CRTP', 'RU', 'CRU', 'DRU', 'CUP', 'DUP', 'DU', 'UU', 'CWU', 'DWU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$getData = $this->UserModel->allUser();
		$res = formatResponse(200, $getData, [], '', [], '');
		$this->response($res, 200);
	}

	public function one_get()
	{
		$permission = checkPermission($this->payload['data']['email'], ['RPRO', 'CPRO', 'UPRO', 'DPRO', 'APRO', 'DEPRO', 'CTEC', 'UTEC', 'CUSI', 'CFED', 'UFED', 'DFED', 'CDIS', 'UDIS', 'DDIS', 'CFLS', 'DFLS', 'CKHSL', 'UKHSL', 'DKHSL', 'CTKHS', 'CMKHS', 'CSAI', 'CFLI', 'DFLI', 'CSAT', 'CFLT', 'DFLT', 'CSV3', 'CDSV3', 'CSL', 'CFLL', 'DFLL', 'CSV4', 'CDV4', 'CSTD', 'URECON', 'CSTP', 'CPTS', 'CRTP', 'RU', 'CRU', 'DRU', 'CUP', 'DUP', 'DU', 'UU', 'CWU', 'DWU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$userCode = $this->get('id');
		if ($userCode == NULL) {
			$res = formatResponse(400, [], [], 'ID user is required', [], '');
			$this->response($res, 400);
		}

		$getData = $this->UserModel->oneUser($userCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data not found', [], '');
			$this->response($res, 404);
		}
		$role = $this->db->select('ru.ruCode,r.roleCode,r.role,r.type')->join('role r', 'r.roleCode=ru.roleCode')->get_where('role_user ru', ['ru.deleteAt' => NULL, 'ru.userCode' => $userCode])->result_array();
		$getData['role'] = $role;
		$res = formatResponse(200, $getData, [], '', [], '');
		$this->response($res, 200);
	}

	public function add_post()
	{
		$permission = checkPermission($this->payload['data']['email'], ['CU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$data = array(
			'name' => $this->post('name'),
			'email' => $this->post('email'),
			'nik_ta' => $this->post('nik_ta'),
			'nik_api' => $this->post('nik_api'),
			'package_id' => $this->post('package_id'),
			'photo' => $this->post('photo'),
			'password' => md5($this->post('password')),
		);

		$make = $this->validator->make($data, [
			'name' => 'required',
			'nik_ta' => 'required',
			'nik_api' => 'required',
			'email' => 'required|email',
			'password' => 'required',
		]);

		$make->setAliases([
			'name' => 'Name',
			'email' => 'Email',
			'nik_ta' => 'NIK TA',
			'nik_api' => 'NIK API',
			'photo' => 'Photo',
			'password' => 'Password',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {

			$cekUnik = $this->UserModel->uniqueEmail($data['email']);
			if ($cekUnik != NUll) {
				$res = formatResponse(400, [], [
					'email' => 'Email already used'
				], '', [], '');
				$this->response($res, 400);
			}
			$cek = $this->UserModel->addUser($data);
			if ($cek) {
				$data = $this->UserModel->oneUser($this->db->insert_id());
				$data = [
					'userCode' => $data['userCode'],
					'name' => $data['name'],
					'email' => $data['email'],
					'nik_ta' => $data['nik_ta'],
					'nik_api' => $data['nik_api'],
					'package_id' => $data['package_id'],
					'photo' => $data['photo'],
					'createAt' => $data['createAt'],
					'updateAt' => $data['updateAt'],
					'deleteAt' => $data['deleteAt'],
				];
				$res = formatResponse(200, $data, [], '', [], 'Success to create user');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed to create user', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function edit_put()
	{
		$permission = checkPermission($this->payload['data']['email'], ['UU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$userCode = $this->get('id');
		if ($userCode == NULL) {
			$res = formatResponse(400, [], [], 'ID user is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->UserModel->oneUser($userCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data not found', [], '');
			$this->response($res, 404);
		}

		$data = array(
			'name' => $this->put('name'),
			'email' => $this->put('email'),
			'nik_ta' => $this->put('nik_ta'),
			'nik_api' => $this->put('nik_api'),
			'package_id' => $this->put('package_id'),
			'photo' => $this->put('photo'),
		);

		$make = $this->validator->make($data, [
			'name' => 'required',
			'nik_ta' => 'required',
			'nik_api' => 'required',
			'email' => 'required|email',
		]);

		$make->setAliases([
			'name' => 'Name',
			'email' => 'Email',
			'nik_ta' => 'NIK TA',
			'nik_api' => 'NIK API',
			'photo' => 'Photo',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {
			$cekUnik = $this->UserModel->uniqueEmail($data['email']);
			if ($cekUnik != NUll) {
				if ($cekUnik['userCode'] != $userCode) {
					$res = formatResponse(400, [], [
						'email' => 'Email already used'
					], '', [], '');
					$this->response($res, 400);
				}
			}
			$cek = $this->UserModel->editUser($data, ['userCode' => $userCode]);
			if ($cek) {
				$data = $this->UserModel->oneUser($userCode);
				$res = formatResponse(200, $data, [], 'Success to update user', [], '');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed to update user', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function delete_delete()
	{
		$permission = checkPermission($this->payload['data']['email'], ['DU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$userCode = $this->get('id');
		if ($userCode == NULL) {
			$res = formatResponse(400, [], [], 'ID user is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->UserModel->oneUser($userCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data not found', [], '');
			$this->response($res, 404);
		} else {
			$cekAkunMaster = $this->UserModel->getRoleUser($userCode);
			if (in_array('Master', array_values(array_column($cekAkunMaster, 'type')))) {
				$res = formatResponse(400, [], [], 'This user cannot be deleted', [], '');
				$this->response($res, 400);
			}
		}
		$cek = $this->UserModel->deleteUser($userCode);
		if ($cek) {
			$res = formatResponse(200, [], [], '', [], 'Success to delete user');
			$this->response($res, 200);
		} else {
			$res = formatResponse(400, [], [], 'Failed to delete user', [], '');
			$this->response($res, 400);
		}
	}

	public function addRole_post()
	{
		$permission = checkPermission($this->payload['data']['email'], ['CRU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$data = array(
			'userCode' => $this->post('userCode'),
			'roleCode' => $this->post('roleCode'),
		);

		$make = $this->validator->make($data, [
			'userCode' => 'required',
			'roleCode' => 'required',
		]);

		$make->setAliases([
			'userCode' => 'User',
			'roleCode' => 'Role',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {
			$getDataUser = $this->UserModel->oneUser($this->post('userCode'));
			if ($getDataUser == NULL) {
				$res = formatResponse(400, [], [], 'Data user not found', [], '');
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
			$checkUserRole =  $this->UserModel->cekUserRole($getDataUser['userCode'], $getDataRole['roleCode']);
			if ($checkUserRole != NULL) {
				$res = formatResponse(400, [], [], 'Data is already exist', [], '');
				$this->response($res, 400);
			}
			$cek = $this->UserModel->addUserRole($data);
			if ($cek) {
				$res = formatResponse(200, [], [], '', [], 'Success add role to user');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed add role to user', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function deleteRole_delete()
	{
		$permission = checkPermission($this->payload['data']['email'], ['DRU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$ruCode = $this->get('id');
		if ($ruCode == NULL) {
			$res = formatResponse(400, [], [], 'ID user role is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->UserModel->oneUserRole($ruCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data user role not found', [], '');
			$this->response($res, 404);
		}
		$getDataUser = $this->UserModel->oneUser($getData['userCode']);
		if ($getDataUser == NULL) {
			$res = formatResponse(404, [], [], 'Data user not found', [], '');
			$this->response($res, 404);
		}
		$getDataRole = $this->RoleModel->oneRole($getData['roleCode']);
		if ($getDataRole == NULL) {
			$res = formatResponse(404, [], [], 'Data role not found', [], '');
			$this->response($res, 404);
		} else {
			if ($getDataRole['type'] == 'Master') {
				$res = formatResponse(400, [], [], 'This role user cannot be delete', [], '');
				$this->response($res, 400);
			} else {
				$cek = $this->UserModel->deleteUserRole($ruCode);
				if ($cek) {
					$res = formatResponse(200, [], [], '', [], 'Success delete role from user');
					$this->response($res, 200);
				} else {
					$res = formatResponse(400, [], [], 'Failed delete role from user', [], '');
					$this->response($res, 400);
				}
			}
		}
	}

	public function addPermission_post()
	{
		$permission = checkPermission($this->payload['data']['email'], ['CUP']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$data = array(
			'permissionCode' => $this->post('permissionCode'),
			'userCode' => $this->post('userCode'),
		);

		$make = $this->validator->make($data, [
			'permissionCode' => 'required',
			'userCode' => 'required',
		]);

		$make->setAliases([
			'permissionCode' => 'Permission',
			'userCode' => 'Role',
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
			$getDataUser = $this->UserModel->oneUser($this->post('userCode'));
			if ($getDataUser == NULL) {
				$res = formatResponse(400, [], [], 'Data user not found', [], '');
				$this->response($res, 404);
			}
			$checkUserPermission =  $this->UserModel->cekUserPermission($getDataUser['userCode'], $getDataPermission['permissionCode']);
			if ($checkUserPermission != NULL) {
				$res = formatResponse(400, [], [], 'Data is already exist', [], '');
				$this->response($res, 400);
			}

			$cek = $this->UserModel->addUserPermission($data);
			if ($cek) {
				$res = formatResponse(200, [], [], '', [], 'Success add permission to user');
				$this->response($res, 200);
			} else {
				$res = formatResponse(400, [], [], 'Failed add permission to user', [], '');
				$this->response($res, 400);
			}
		}
	}

	public function deletePermission_delete()
	{
		$permission = checkPermission($this->payload['data']['email'], ['DUP']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$upCode = $this->get('id');
		if ($upCode == NULL) {
			$res = formatResponse(400, [], [], 'ID role permission is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->UserModel->oneUserPermission($upCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data user permission not found', [], '');
			$this->response($res, 404);
		}
		$getDataPermission = $this->PermissionModel->onePermission($getData['permissionCode']);
		if ($getDataPermission == NULL) {
			$res = formatResponse(404, [], [], 'Data permission not found', [], '');
			$this->response($res, 404);
		}
		$getDataUser = $this->UserModel->oneUser($getData['userCode']);
		if ($getDataUser == NULL) {
			$res = formatResponse(404, [], [], 'Data user not found', [], '');
			$this->response($res, 404);
		}

		$cek = $this->UserModel->deleteUserPermission($upCode);
		if ($cek) {
			$res = formatResponse(200, [], [], '', [], 'Success delete permission from user');
			$this->response($res, 200);
		} else {
			$res = formatResponse(400, [], [], 'Failed delete permission from user', [], '');
			$this->response($res, 400);
		}
	}

	public function detailPermission_get()
	{
		$permission = checkPermission($this->payload['data']['email'], ['RDRMPU']);
		if ($permission['status'] == false) {
			$this->response($permission['data'], 400);
		}
		$userCode = $this->get('id');
		if ($userCode == NULL) {
			$res = formatResponse(400, [], [], 'ID user is required', [], '');
			$this->response($res, 400);
		}
		$getData = $this->UserModel->oneUser($userCode);
		if ($getData == NULL) {
			$res = formatResponse(404, [], [], 'Data user not found', [], '');
			$this->response($res, 404);
		}
		$return = [];
		$role = $this->db
			->join('role u', 'u.roleCode=ru.roleCode')
			->where(['ru.userCode' => $userCode, 'ru.deleteAt' => NULL])
			->get('role_user ru')->result_array();
		$spesialPermission = $this->UserModel->allUserPermission($userCode);
		$return['role'] = $role;
		$return['spesialPermission'] = $spesialPermission;
		$res = formatResponse(200, $return, [], '', [], '');
		$this->response($res, 200);
	}
}
