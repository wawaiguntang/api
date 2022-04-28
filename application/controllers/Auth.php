<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Auth extends RestController
{
	private $validator;
	public function __construct()
	{
		parent::__construct();
		$this->validator = new Validator();
		$this->load->model('AuthModel');
	}
	public function login_post()
	{
		$data = array(
			'email' => $this->post('email'),
			'password' => $this->post('password'),
		);

		$make = $this->validator->make($data, [
			'email' => 'required|email',
			'password' => 'required'
		]);

		$make->setAliases([
			'email' => 'Email',
			'password' => 'Password',
		]);

		$make->validate();

		if ($make->fails()) {
			$errors = $make->errors();
			$err = $errors->firstOfAll();
			$res = formatResponse(400, [], $err, '', [], '');
			$this->response($res, 400);
		} else {
			$cek = $this->AuthModel->cekEmailAndPassword($data['email'], $data['password']);
			if ($cek == NULL) {
				$res = formatResponse(400, [], [], 'Your email or password is wrong', [], '');
				$this->response($res, 400);
			} else {
				$data = generateAccessToken($cek['email']);
				$res = formatResponse(200, $data, [], '', [], 'Your email or password is correct');
				$this->response($res, 200);
			}
		}
	}

	public function refresh_get()
	{
		$payload = JWT_Verif_Refresh();
		if ($payload['status'] == false) {
			$res = formatResponse(400, [], [], $payload['message'], [], '');
			$this->response($res, 400);
		} else {
			$data = generateAccessToken($payload['data']['email']);
			$res = formatResponse(200, $data, [], '', [], 'Your tokens have been updated');
			$this->response($res, 200);
		}
	}

	public function pre_get()
	{
		$access = [
			'Permission' => [
				[
					'permission' => 'RMP',
					'description' => 'See permission and module'
				]
			],
			'Role' => [
				[
					'permission' => 'RR',
					'description' => 'See roles'
				],
				[
					'permission' => 'CR',
					'description' => 'Create role'
				],
				[
					'permission' => 'UR',
					'description' => 'Update role'
				],
				[
					'permission' => 'DR',
					'description' => 'Delete role'
				],
				[
					'permission' => 'CRP',
					'description' => 'Add permission to role'
				],
				[
					'permission' => 'DRP',
					'description' => 'Delete permission from role'
				],
				[
					'permission' => 'RRP',
					'description' => 'See module and permission by role'
				],
			],
			'User' => [
				[
					'permission' => 'RU',
					'description' => 'See users'
				],
				[
					'permission' => 'CU',
					'description' => 'Create user'
				],
				[
					'permission' => 'UU',
					'description' => 'Update user'
				],
				[
					'permission' => 'DU',
					'description' => 'Delete user'
				],
				[
					'permission' => 'CRU',
					'description' => 'Add role to user'
				],
				[
					'permission' => 'DRU',
					'description' => 'Delete role from user'
				],
				[
					'permission' => 'CUP',
					'description' => 'Add permission to user'
				],
				[
					'permission' => 'DUP',
					'description' => 'Delete permission from user'
				],
				[
					'permission' => 'RDRMPU',
					'description' => 'Read role, module, permission from user'
				],

			],
			'Brand' => [
				[
					'permission' => 'RB',
					'description' => 'See brands'
				],
				[
					'permission' => 'CB',
					'description' => 'Create brand'
				],
				[
					'permission' => 'UB',
					'description' => 'Update brand'
				],
				[
					'permission' => 'DB',
					'description' => 'Delete brand'
				],
			],
			'Product' => [
				[
					'permission' => 'RPP',
					'description' => 'See products'
				],
				[
					'permission' => 'CPP',
					'description' => 'Create product'
				],
				[
					'permission' => 'UPP',
					'description' => 'Update product'
				],
				[
					'permission' => 'DPP',
					'description' => 'Delete product'
				],
			],
			'Region' => [
				[
					'permission' => 'RRR',
					'description' => 'See regions'
				],
				[
					'permission' => 'CRR',
					'description' => 'Create region'
				],
				[
					'permission' => 'URR',
					'description' => 'Update region'
				],
				[
					'permission' => 'DRR',
					'description' => 'Delete region'
				],
			],
			'Witel' => [
				[
					'permission' => 'RW',
					'description' => 'See witels'
				],
				[
					'permission' => 'CW',
					'description' => 'Create witel'
				],
				[
					'permission' => 'UW',
					'description' => 'Update witel'
				],
				[
					'permission' => 'DW',
					'description' => 'Delete witel'
				],
				[
					'permission' => 'RWUU',
					'description' => 'See user from witel'
				],
				[
					'permission' => 'CWU',
					'description' => 'Add user to witel'
				],
				[
					'permission' => 'DWU',
					'description' => 'Delete user from witel'
				],

			],
			'Job' => [
				[
					'permission' => 'RJ',
					'description' => 'See jobs'
				],
				[
					'permission' => 'CJ',
					'description' => 'Create job'
				],
				[
					'permission' => 'UJ',
					'description' => 'Update job'
				],
				[
					'permission' => 'DJ',
					'description' => 'Delete job'
				]
			],
			'Supplier' => [
				[
					'permission' => 'RS',
					'description' => 'See suppliers'
				],
				[
					'permission' => 'CS',
					'description' => 'Create supplier'
				],
				[
					'permission' => 'US',
					'description' => 'Update supplier'
				],
				[
					'permission' => 'DS',
					'description' => 'Delete supplier'
				]
			],
			'Package' => [
				[
					'permission' => 'RP',
					'description' => 'See packages'
				],
				[
					'permission' => 'CP',
					'description' => 'Create package'
				],
				[
					'permission' => 'UP',
					'description' => 'Update package'
				],
				[
					'permission' => 'DP',
					'description' => 'Delete package'
				],
				[
					'permission' => 'RPD',
					'description' => 'See designator and price from package'
				]

			],
			'Designator' => [
				[
					'permission' => 'RD',
					'description' => 'See designators'
				],
				[
					'permission' => 'CD',
					'description' => 'Create designator'
				],
				[
					'permission' => 'UD',
					'description' => 'Update designator'
				],
				[
					'permission' => 'DD',
					'description' => 'Delete designator'
				],
				[
					'permission' => 'CDPP',
					'description' => 'Add designator to package'
				],
				[
					'permission' => 'DDPP',
					'description' => 'Delete designator from package'
				]

			],
			'PurchaseOrder' => [
				[
					'permission' => 'RPO',
					'description' => 'See purchase orders'
				],
				[
					'permission' => 'CPO',
					'description' => 'Create purchase order'
				],
				[
					'permission' => 'UPO',
					'description' => 'Update purchase order'
				],
				[
					'permission' => 'DPO',
					'description' => 'Delete purchase order'
				],
				[
					'permission' => 'UCPO',
					'description' => 'Add charge purchase order'
				],
				[
					'permission' => 'RPOI',
					'description' => 'See item purchase order'
				],
				[
					'permission' => 'CPOI',
					'description' => 'Add item purchase order'
				],
				[
					'permission' => 'UPOI',
					'description' => 'Update item purchase order'
				],
				[
					'permission' => 'DPOI',
					'description' => 'Delete item purchase order'
				],
				[
					'permission' => 'USPOITP',
					'description' => 'Update status PO from issued to processed'
				],
				[
					'permission' => 'USPOPTD',
					'description' => 'Update status PO from processed to done'
				],
			],
			'DeliveryOrder' => [
				[
					'permission' => 'RDO',
					'description' => 'See delevery orders'
				],
				[
					'permission' => 'CDO',
					'description' => 'Create delevery order'
				],
				[
					'permission' => 'UDO',
					'description' => 'Update delevery order'
				],
				[
					'permission' => 'DDO',
					'description' => 'Delete delevery order'
				],
				[
					'permission' => 'RDOBW',
					'description' => 'See delevery order on witel'
				],
				[
					'permission' => 'UCDO',
					'description' => 'Add charge delevery order'
				],
				[
					'permission' => 'RDOI',
					'description' => 'See item delevery order'
				],
				[
					'permission' => 'CDOI',
					'description' => 'Add item delevery order'
				],
				[
					'permission' => 'UDOI',
					'description' => 'Update item delevery order'
				],
				[
					'permission' => 'DDOI',
					'description' => 'Delete item delevery order'
				],
				[
					'permission' => 'USDOITP',
					'description' => 'Update status DO from issued to processed'
				],
				[
					'permission' => 'USDOPTD',
					'description' => 'Update status DO from processed to done'
				],

			],
			'ReciveOrder' => [
				[
					'permission' => 'RROPODO',
					'description' => 'See delevery orders and purchase orders'
				],
				[
					'permission' => 'RROPODOBW',
					'description' => 'See delevery orders by witel'
				],
			],
			'Project' => [
				[
					'permission' => 'RPRO',
					'description' => 'See project'
				],
				[
					'permission' => 'CPRO',
					'description' => 'Create project'
				],
				[
					'permission' => 'UPRO',
					'description' => 'Update project'
				],
				[
					'permission' => 'DPRO',
					'description' => 'Delete project'
				],
				[
					'permission' => 'APRO',
					'description' => 'Approve project'
				],
				[
					'permission' => 'DEPRO',
					'description' => 'Decline project'
				],
				[
					'permission' => 'CTEC',
					'description' => 'Add technician'
				],
				[
					'permission' => 'UTEC',
					'description' => 'Update technician'
				],
				[
					'permission' => 'CUSI',
					'description' => 'Create and update sitax'
				],
				[
					'permission' => 'CFED',
					'description' => 'Create feeder'
				],
				[
					'permission' => 'UFED',
					'description' => 'Update feeder'
				],
				[
					'permission' => 'DFED',
					'description' => 'Delete feeder'
				],
				[
					'permission' => 'CDIS',
					'description' => 'Create distribusi'
				],
				[
					'permission' => 'UDIS',
					'description' => 'Update distribusi'
				],
				[
					'permission' => 'DDIS',
					'description' => 'Delete distribusi'
				],
				[
					'permission' => 'CFLS',
					'description' => 'Add file survey'
				],
				[
					'permission' => 'DFLS',
					'description' => 'Delete file survey'
				],
				[
					'permission' => 'CKHSL',
					'description' => 'Add list khs'
				],
				[
					'permission' => 'UKHSL',
					'description' => 'Update list khs'
				],
				[
					'permission' => 'DKHSL',
					'description' => 'Delete list khs'
				],
				[
					'permission' => 'CTKHS',
					'description' => 'Change status from survey to KHS Check'
				],
				[
					'permission' => 'CMKHS',
					'description' => 'Select source material'
				],
				[
					'permission' => 'CSAI',
					'description' => 'Change status from instalation to approve instalation'
				],
				[
					'permission' => 'CFLI',
					'description' => 'Add file instalation'
				],
				[
					'permission' => 'DFLI',
					'description' => 'Delete file instalation'
				],
				[
					'permission' => 'CSAT',
					'description' => 'Change status from approve instalation to termination'
				],
				[
					'permission' => 'CFLT',
					'description' => 'Add file termination'
				],
				[
					'permission' => 'DFLT',
					'description' => 'Delete file termination'
				],
				[
					'permission' => 'CSV3',
					'description' => 'Change status from termination to valid 3'
				],
				[
					'permission' => 'CDSV3',
					'description' => 'Complete data in step valid 3'
				],
				[
					'permission' => 'CSL',
					'description' => 'Change status from valid 3 to labeling'
				],
				[
					'permission' => 'CFLL',
					'description' => 'Add file labeling'
				],
				[
					'permission' => 'DFLL',
					'description' => 'Delete file labeling'
				],
				[
					'permission' => 'CSV4',
					'description' => 'Change status from labeling to valid 4'
				],
				[
					'permission' => 'CDV4',
					'description' => 'Complete data in step valid 4'
				],
				[
					'permission' => 'CSTD',
					'description' => 'Change status from valid 4 to done'
				],
			]
		];



		foreach ($access as $k => $v) {
			$this->db->insert('module', ['module' => $k]);
			$id = $this->db->insert_id();
			foreach ($v as $kk => $vvv) {
				$data = [
					'permission' => $vvv['permission'],
					'description' => $vvv['description'],
					'moduleCode' => $id
				];
				$this->db->insert('permission', $data);
			}
		}
		$data = [];
		$permission = $this->db->get('permission')->result_array();
		foreach ($permission  as $k => $v) {
			$data[] = [
				'roleCode' => 1,
				'permissionCode' => $v['permissionCode']
			];
		}
		$this->db->insert_batch('role_permission', $data);
	}

	public function test_get()
	{
		$postDetailFormat = [
			'feeder' => [
				[
					'project_id' => '',
					'feeder_odc' => '',
					'feeder_capacity' => '',
					'feeder_address' => '',
					'feeder_lg' => '',
					'feeder_lt' => '',
					'feeder_port' => '',
					'feeder_core' => '',
					'distribusi' => [
						[
							'distribusi_kukd' => '',
							'distribusi_odp' => '',
							'distribusi_address' => '',
							'distribusi_lg' => '',
							'distribusi_lt' => '',
							'distribusi_core' => '',
							'distribusi_core_opsi' => '',
							'distribusi_capacity' => '',
							'distribusi_note' => '',
						],
						[
							'distribusi_kukd' => '',
							'distribusi_odp' => '',
							'distribusi_address' => '',
							'distribusi_lg' => '',
							'distribusi_lt' => '',
							'distribusi_core' => '',
							'distribusi_core_opsi' => '',
							'distribusi_capacity' => '',
							'distribusi_note' => '',
						]
					],
				],
				[
					'project_id' => '',
					'feeder_odc' => '',
					'feeder_capacity' => '',
					'feeder_address' => '',
					'feeder_lg' => '',
					'feeder_lt' => '',
					'feeder_port' => '',
					'feeder_core' => '',
					'distribusi' => [
						[
							'distribusi_kukd' => '',
							'distribusi_odp' => '',
							'distribusi_address' => '',
							'distribusi_lg' => '',
							'distribusi_lt' => '',
							'distribusi_core' => '',
							'distribusi_core_opsi' => '',
							'distribusi_capacity' => '',
							'distribusi_note' => '',
						],
						[
							'distribusi_kukd' => '',
							'distribusi_odp' => '',
							'distribusi_address' => '',
							'distribusi_lg' => '',
							'distribusi_lt' => '',
							'distribusi_core' => '',
							'distribusi_core_opsi' => '',
							'distribusi_capacity' => '',
							'distribusi_note' => '',
						]
					],
				]
			],
			'image' => [
				'sad',
				'asd',
			]
		];
		$res = formatResponse(200, $postDetailFormat, [], '', [], 'Your tokens have been updated');
		$this->response($res, 200);
	}
}
