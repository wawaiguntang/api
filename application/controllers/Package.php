<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Package extends RestController
{
    private $validator;
    private $payload;
    public function __construct()
    {
        parent::__construct();
        $this->validator = new Validator();
        $this->load->model('GlobalModel');
        $this->payload = JWT_Verif_Access();
        if ($this->payload['status'] == false) {
            $res = formatResponse(400, [], [], $this->payload['message'], [], '');
            $this->response($res, 400);
        }
    }

    public function all_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RP', 'CDPP', 'DDPP', 'DP', 'UP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->GlobalModel->getData('package', ['deleteAt' => NULL]);
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RP', 'CDPP', 'DDPP', 'DP', 'UP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $package_id = $this->get('id');
        if ($package_id == NULL) {
            $res = formatResponse(400, [], [], 'ID package is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->GlobalModel->getData('package', ['package_id' => $package_id, 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data package not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'package_name' => $this->post('package_name'),
            'package_desc' => $this->post('package_desc'),
        );

        $make = $this->validator->make($data, [
            'package_name' => 'required',
            'package_desc' => 'required',
        ]);

        $make->setAliases([
            'package_name' => 'Package Name',
            'package_desc' => 'Package Description',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->insert('package', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('package', ['package_id' => $this->db->insert_id()], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create package');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create package', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $package_id = $this->get('id');
        if ($package_id == NULL) {
            $res = formatResponse(400, [], [], 'ID package is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('package', ['deleteAt' => NULL, 'package_id' => $package_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data package not found', [], '');
            $this->response($res, 404);
        }

        $data = array(
            'package_name' => $this->put('package_name'),
            'package_desc' => $this->put('package_desc'),
        );

        $make = $this->validator->make($data, [
            'package_name' => 'required',
            'package_desc' => 'required',
        ]);

        $make->setAliases([
            'package_name' => 'Package Name',
            'package_desc' => 'Package Description',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {

            $cek = $this->GlobalModel->update('package', $data, ['package_id' => $package_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('package', ['package_id' => $package_id], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update package');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update package', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $package_id = $this->get('id');
        if ($package_id == NULL) {
            $res = formatResponse(400, [], [], 'ID package is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('package', ['deleteAt' => NULL, 'package_id' => $package_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data package not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('package', ['package_id' => $package_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete package');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed to delete package', [], '');
            $this->response($res, 400);
        }
    }

    public function designator_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RPD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $package_id = $this->get('id');
        if ($package_id == NULL) {
            $res = formatResponse(400, [], [], 'ID package is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('package', ['package_id' => $package_id, 'deleteAt' => NULL], false);
        $return['package'] = $getData;
        $return['designator'] = [];
        $getDesignator = $this->db
            ->select('d.designator_id,d.designator_code,d.designator_desc,dp.material_price,dp.service_price,d.createAt,d.updateAt,d.deleteAt')
            ->join('designator d', 'd.designator_id=dp.designator_id')
            ->where(['dp.deleteAt' => NULL, 'dp.package_id' => $package_id])
            ->get('designator_package dp')
            ->result_array();
        $material_price = 0;
        $service_price = 0;
        foreach ($getDesignator as $k =>  $v) {
            $material_price += $v['material_price'];
            $service_price += $v['service_price'];
            $return['designator'][] = $v;
        }
        $return['total_material_price'] = $material_price;
        $return['total_service_price'] = $service_price;
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }
}
