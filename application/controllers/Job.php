<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Job extends RestController
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
        $permission = checkPermission($this->payload['data']['email'], ['RJ', 'DJ', 'UJ']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $getData = $this->GlobalModel->getData('job', ['deleteAt' => NULL]);
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RJ', 'DJ', 'UJ']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $job_id = $this->get('id');
        if ($job_id == NULL) {
            $res = formatResponse(400, [], [], 'ID job is required', [], '');
            $this->response($res, 400);
        }

        $getData = $this->GlobalModel->getData('job', ['job_id' => $job_id, 'deleteAt' => NULL], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data job not found', [], '');
            $this->response($res, 404);
        }
        $res = formatResponse(200, $getData, [], '', [], '');
        $this->response($res, 200);
    }

    // public function add_post()
    // {
    //     $permission = checkPermission($this->payload['data']['email'], ['CJ']);
    //     if ($permission['status'] == false) {
    //         $this->response($permission['data'], 400);
    //     }
    //     $data = array(
    //         'job_name' => $this->post('job_name'),
    //         'job_percent' => $this->post('job_percent'),
    //         'job_day' => $this->post('job_day'),
    //     );

    //     $make = $this->validator->make($data, [
    //         'job_name' => 'required',
    //         'job_percent' => 'required|numeric',
    //         'job_day' => 'required|numeric',
    //     ]);

    //     $make->setAliases([
    //         'job_name' => 'Job Name',
    //         'job_percent' => 'Percent',
    //         'job_day' => 'Day',
    //     ]);

    //     $make->validate();

    //     if ($make->fails()) {
    //         $errors = $make->errors();
    //         $err = $errors->firstOfAll();
    //         $res = formatResponse(400, [], $err, '', [], '');
    //         $this->response($res, 400);
    //     } else {
    //         $cek = $this->GlobalModel->insert('job', $data);
    //         if ($cek) {
    //             $data = $this->GlobalModel->getData('job', ['job_id' => $this->db->insert_id()], false);
    //             $res = formatResponse(200, $data, [], '', [], 'Success to create job');
    //             $this->response($res, 200);
    //         } else {
    //             $res = formatResponse(400, [], [], 'Failed to create job', [], '');
    //             $this->response($res, 400);
    //         }
    //     }
    // }

    // public function edit_put()
    // {
    //     $permission = checkPermission($this->payload['data']['email'], ['UJ']);
    //     if ($permission['status'] == false) {
    //         $this->response($permission['data'], 400);
    //     }
    //     $job_id = $this->get('id');
    //     if ($job_id == NULL) {
    //         $res = formatResponse(400, [], [], 'ID job is required', [], '');
    //         $this->response($res, 400);
    //     }
    //     $getData = $this->GlobalModel->getData('job', ['deleteAt' => NULL, 'job_id' => $job_id], false);
    //     if ($getData == NULL) {
    //         $res = formatResponse(404, [], [], 'Data job not found', [], '');
    //         $this->response($res, 404);
    //     }

    //     $data = array(
    //         'job_name' => $this->put('job_name'),
    //         'job_percent' => $this->put('job_percent'),
    //         'job_day' => $this->put('job_day'),
    //     );

    //     $make = $this->validator->make($data, [
    //         'job_name' => 'required',
    //         'job_percent' => 'required|numeric',
    //         'job_day' => 'required|numeric',
    //     ]);

    //     $make->setAliases([
    //         'job_name' => 'Job Name',
    //         'job_percent' => 'Percent',
    //         'job_day' => 'Day',
    //     ]);

    //     $make->validate();

    //     if ($make->fails()) {
    //         $errors = $make->errors();
    //         $err = $errors->firstOfAll();
    //         $res = formatResponse(400, [], $err, '', [], '');
    //         $this->response($res, 400);
    //     } else {

    //         $cek = $this->GlobalModel->update('job', $data, ['job_id' => $job_id]);
    //         if ($cek) {
    //             $data = $this->GlobalModel->getData('job', ['job_id' => $job_id], false);
    //             $res = formatResponse(200, $data, [], '', [], 'Success to update job');
    //             $this->response($res, 200);
    //         } else {
    //             $res = formatResponse(400, [], [], 'Failed to update job', [], '');
    //             $this->response($res, 400);
    //         }
    //     }
    // }

    // public function delete_delete()
    // {
    //     $permission = checkPermission($this->payload['data']['email'], ['DJ']);
    //     if ($permission['status'] == false) {
    //         $this->response($permission['data'], 400);
    //     }
    //     if ($permission['status'] == false) {
    //         $this->response($permission['data'], 400);
    //     }
    //     $job_id = $this->get('id');
    //     if ($job_id == NULL) {
    //         $res = formatResponse(400, [], [], 'ID job is required', [], '');
    //         $this->response($res, 400);
    //     }
    //     $getData = $this->GlobalModel->getData('job', ['deleteAt' => NULL, 'job_id' => $job_id]);
    //     if ($getData == NULL) {
    //         $res = formatResponse(404, [], [], 'Data job not found', [], '');
    //         $this->response($res, 404);
    //     }
    //     $cek = $this->GlobalModel->delete('job', ['job_id' => $job_id]);
    //     if ($cek) {
    //         $res = formatResponse(200, [], [], '', [], 'Success to delete job');
    //         $this->response($res, 200);
    //     } else {
    //         $res = formatResponse(200, [], [], 'Failed to delete job', [], '');
    //         $this->response($res, 400);
    //     }
    // }
}
