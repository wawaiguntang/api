<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class DesignatorPackage extends RestController
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


    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CDPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'designator_id' => $this->post('designator_id'),
            'package_id' => $this->post('package_id'),
            'material_price' => $this->post('material_price'),
            'service_price' => $this->post('service_price'),
        );

        $make = $this->validator->make($data, [
            'designator_id' => 'required',
            'package_id' => 'required',
            'material_price' => 'required|numeric',
            'service_price' => 'required|numeric',
        ]);

        $make->setAliases([
            'designator_id' => 'Designator',
            'package_id' => 'Package',
            'material_price' => 'Material price',
            'service_price' => 'Service price',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $designator = $this->GlobalModel->getData('designator', ['deleteAt' => NULL, 'designator_id' => $data['designator_id']]);
            if ($designator == NULL) {
                $res = formatResponse(400, [], [], 'Data designator not found', [], '');
                $this->response($res, 400);
            }
            $package = $this->GlobalModel->getData('package', ['deleteAt' => NULL, 'package_id' => $data['package_id']]);
            if ($package == NULL) {
                $res = formatResponse(400, [], [], 'Data package not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->insert('designator_package', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('designator_package', ['designator_package_id' => $this->db->insert_id()], false);

                $res = formatResponse(200, $data, [], '', [], 'Success to create designator package');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create designator package', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DDPP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $designator_package_id = $this->get('id');
        if ($designator_package_id == NULL) {
            $res = formatResponse(400, [], [], 'ID designator package is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('designator_package', ['deleteAt' => NULL, 'designator_package_id' => $designator_package_id]);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data designator package not found', [], '');
            $this->response($res, 404);
        }
        $cek = $this->GlobalModel->delete('designator_package', ['designator_package_id' => $designator_package_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success delete package from designator');
            $this->response($res, 200);
        } else {
            $res = formatResponse(200, [], [], 'Failed delete package from designator', [], '');
            $this->response($res, 400);
        }
    }
}
