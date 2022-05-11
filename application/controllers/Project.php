<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Rakit\Validation\Validator;

class Project extends RestController
{
    private $validator;
    private $payload;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new Validator();
        $this->load->model('GlobalModel');
        $this->load->model('UserModel');
        $this->payload = JWT_Verif_Access();
        if ($this->payload['status'] == false) {
            $res = formatResponse(400, [], [], $this->payload['message'], [], '');
            $this->response($res, 400);
        }
    }

    public function all_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RJOB', 'UJOB', 'RPRO', 'CPRO', 'UPRO', 'DPRO', 'APRO', 'DEPRO', 'CTEC', 'UTEC', 'CUSI', 'CFED', 'UFED', 'DFED', 'CDIS', 'UDIS', 'DDIS', 'CFLS', 'DFLS', 'CKHSL', 'UKHSL', 'DKHSL', 'CTKHS', 'CMKHS', 'CSAI', 'CFLI', 'DFLI', 'CSAT', 'CFLT', 'DFLT', 'CSV3', 'CDSV3', 'CSL', 'CFLL', 'DFLL', 'CSV4', 'CDV4', 'CSTD', 'URECON', 'CSTP', 'CPTS', 'CRTP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->join('witel ww', 'ww.witel_id=w.witel_id')->get_where('witel_user w', ['ww.deleteAt' => NULL, 'w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email']])->row_array();
        if ($check == NULL) {
            $where = [];
        } else {
            $where = [
                'project.witel_id' => $check['witel_id']
            ];
        }
        $data = $this->db
            ->select('project_khs.khs_source,project.project_id,project.project_code,project.project_date,project.project_status,project_cat.cat_name,witel.witel_code,witel.witel_name,region.region_name')
            ->join('project_cat', 'project_cat.cat_id=project.cat_id')
            ->join('witel', 'witel.witel_id=project.witel_id')
            ->join('region', 'region.region_id=witel.region_id')
            ->join('project_khs', 'project_khs.project_id=project.project_id', 'left')
            ->where('project.deleteAt', NULL)
            ->where($where)
            ->get('project')
            ->result_array();
        $res = formatResponse(200, $data, [], '', [], '');
        $this->response($res, 200);
    }

    public function one_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RJOB', 'UJOB', 'RPRO', 'CPRO', 'UPRO', 'DPRO', 'APRO', 'DEPRO', 'CTEC', 'UTEC', 'CUSI', 'CFED', 'UFED', 'DFED', 'CDIS', 'UDIS', 'DDIS', 'CFLS', 'DFLS', 'CKHSL', 'UKHSL', 'DKHSL', 'CTKHS', 'CMKHS', 'CSAI', 'CFLI', 'DFLI', 'CSAT', 'CFLT', 'DFLT', 'CSV3', 'CDSV3', 'CSL', 'CFLL', 'DFLL', 'CSV4', 'CDV4', 'CSTD', 'URECON', 'CSTP', 'CPTS', 'CRTP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        $khs_source = ($this->get('khs_source') == NULL) ? 'WITEL' : $this->get('khs_source');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->join('witel ww', 'ww.witel_id=w.witel_id')->get_where('witel_user w', ['ww.deleteAt' => NULL, 'w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email']])->row_array();
        if ($check == NULL) {
            $where = [];
        } else {
            $where = [
                'project.witel_id' => $check['witel_id']
            ];
        }
        $getData = $this->db
            ->select('project.userCode,project.project_start,project.project_done,project.project_reconsiliasi,project.label_cat,project.project_note,project.cat_id,project.project_id,project.project_code,project.project_date,project.project_status,project_cat.cat_name,witel.witel_id,witel.witel_code,witel.witel_name,region.region_name')
            ->join('project_cat', 'project_cat.cat_id=project.cat_id')
            ->join('witel', 'witel.witel_id=project.witel_id')
            ->join('region', 'region.region_id=witel.region_id')
            ->where('project.deleteAt', NULL)
            ->where('project.project_id', $project_id)
            // ->where($where)
            ->get('project')
            ->row_array();
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        }
        $return = $getData;
        $return['estimate_date'] = $this->getRange($getData['project_start'], $getData['project_done']);
        $return['project_reconsiliasi'] = json_decode($getData['project_reconsiliasi'], true);
        // $return['feeder'] = [];
        // $feeder = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);
        // foreach ($feeder as $k => $v) {
        //     $v['distribusi'] = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'feeder_id' => $v['feeder_id']]);
        //     $return['feeder'][] = $v;
        // }
        $sitax = $this->GlobalModel->getData('project_sitax', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($sitax == NULL) {
            $return['sitax'] = [];
        } else {
            if ($sitax['sitax_list'] == NULL) {
                $return['sitax']['sitax_type'] = 'non-sitax';
            } else {
                $return['sitax']['sitax_type'] = 'sitax';
                $return['sitax']['sitax_list'] = json_decode($sitax['sitax_list'], true);
                $return['sitax']['sitax_total'] = $sitax['sitax_total'];
            }
        }
        $return['survey'] = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id]);
        $technician = $this->db
            ->select('pu.user_id,u.name,u.userCode,pu.user_leader')
            ->join('user u', 'u.userCode=pu.userCode')
            ->where('pu.project_id', $project_id)
            ->where('pu.deleteAt', NULL)
            ->get('project_user pu')
            ->result_array();
        $return['technician'] = $technician;
        $return['khs'] = [];
        if ($getData['project_status'] == 'Survey') {
            $khs = $this->db
                ->get_where('project_khs pk', ['pk.deleteAt' => NULL, 'pk.project_id' => $project_id])
                ->result_array();
            foreach ($khs as $k => $s) {
                $khslist = $this->db
                    ->join('designator d', 'd.designator_id=pkl.designator_id')
                    ->join('product p', 'p.product_id=d.product_id')
                    ->join('brand b', 'b.brand_id=p.brand_id')
                    ->get_where('project_khs_list pkl', ['pkl.deleteAt' => NULL, 'pkl.khs_id' => $s['khs_id']])
                    ->result_array();
                $temp = [];
                foreach ($khslist as $k => $v) {
                    if ($v['tipe'] == 'Feeder') {
                        $dataTipe = $this->db->get_where('project_feeder', ['deleteAt' => NULL, 'project_feeder_id' => $v['tipe_id']])->row_array();
                    } elseif ($v['tipe'] == 'Penggelaran') {
                        $dataTipe = $this->db->get_where('project_penggelaran', ['deleteAt' => NULL, 'project_penggelaran_id' => $v['tipe_id']])->row_array();
                    } elseif ($v['tipe'] == 'ODP') {
                        $dataTipe = $this->db->get_where('project_odc', ['deleteAt' => NULL, 'project_odc_id' => $v['tipe_id']])->row_array();
                    } elseif ($v['tipe'] == 'ODC') {
                        $dataTipe = $this->db->get_where('project_odp', ['deleteAt' => NULL, 'project_odp_id' => $v['tipe_id']])->row_array();
                    } elseif ($v['tipe'] == 'GPON') {
                        $dataTipe = $this->db->get_where('project_gpon', ['deleteAt' => NULL, 'project_gpon_id' => $v['tipe_id']])->row_array();
                    } else {
                        $res = formatResponse(404, [], [], 'Tipe not found', [], '');
                        $this->response($res, 404);
                    }
                    $temp[] = [
                        'khs_list_id' => $v['khs_list_id'],
                        'khs_list_qty' => $v['khs_list_qty'],
                        'designator_id' => $v['designator_id'],
                        'designator_code' => $v['designator_code'],
                        'designator_desc' => $v['designator_desc'],
                        'product_name' => $v['product_name'],
                        'product_portion' => $v['product_portion'],
                        'brand_name' => $v['brand_name'],
                        'tipe' => $v['tipe'],
                        'tipe_id' => $v['tipe_id'],
                        'data' => $dataTipe
                    ];
                }
                $s['khs_list'] = $temp;
                $tempKHS = $s;
                $return['khs'][] = $s;
            }
        }
        // if ($getData['project_status'] == 'KHS Check') {
        //     $getDataUser = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        //     if ($getDataUser == NULL) {
        //         $res = formatResponse(404, [], [], 'Data user not found', [], '');
        //         $this->response($res, 404);
        //     }
        //     $getDataPackage = $this->GlobalModel->getData('package', ['deleteAt' => NULL, 'package_id' => $getDataUser['package_id']], false);
        //     if ($getDataPackage == NULL) {
        //         $res = formatResponse(404, [], [], 'Data package from user not found', [], '');
        //         $this->response($res, 404);
        //     }
        //     $dataKHSList = [];
        //     $getDataKHSList = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'project_id' => $project_id]);
        //     foreach ($getDataKHSList as $k => $v) {
        //         $getDesignator = $this->db
        //             ->select('d.designator_id,d.designator_code,d.designator_desc,dp.material_price,dp.service_price,d.createAt,d.updateAt,d.deleteAt')
        //             ->join('designator d', 'd.designator_id=dp.designator_id')
        //             ->where(['dp.deleteAt' => NULL, 'dp.package_id' => $getDataUser['package_id'], 'dp.designator_id' => $v['designator_id']])
        //             ->get('designator_package dp')
        //             ->row_array();
        //         $khslist = $this->db
        //             ->join('product p', 'p.product_id=d.product_id')
        //             ->join('brand b', 'b.brand_id=p.brand_id')
        //             ->get_where('designator d', ['d.deleteAt' => NULL, 'd.designator_id' => $v['designator_id']])
        //             ->row_array();
        //         if ($v['stock_id'] == NULL) {
        //             $getStockChose = [];
        //         } else {
        //             $getStockChose = $this->GlobalModel->getData('stock_witel', ['stock_id' => $v['stock_id']], false);
        //         }
        //         $getStock = $this->GlobalModel->getData('stock_witel', ['witel_id' => $getData['witel_id'], 'product_id' => $khslist['product_id'], 'stock_qty >=' => $v['khs_list_qty']]);
        //         if ($getDesignator != NULL) {
        //             $params = [
        //                 'khs_list_id' => $v['khs_list_id'],
        //                 'khs_list_qty' => $v['khs_list_qty'],
        //                 'designator_id' => $getDesignator['designator_id'],
        //                 'designator_code' => $getDesignator['designator_code'],
        //                 'designator_desc' => $getDesignator['designator_desc'],
        //                 'product_name' => $khslist['product_name'],
        //                 'product_portion' => $khslist['product_portion'],
        //                 'brand_name' => $khslist['brand_name'],
        //                 'khs_list_material_price' => $getDesignator['material_price'],
        //                 'khs_list_service_price' => $getDesignator['service_price'],
        //                 'khs_list_material_total' => $getDesignator['material_price'] * $v['khs_list_qty'],
        //                 'khs_list_service_total' => $getDesignator['service_price'] * $v['khs_list_qty'],
        //                 'stock' => $getStock,
        //                 'stock_chosen' => $getStockChose
        //             ];
        //             $dataKHSList[] = $params;
        //         }
        //     }
        //     $return['khs']['khs_list'] = $dataKHSList;
        //     //sampe sini
        //     $material_price = 0;
        //     $service_price = 0;
        //     foreach ($dataKHSList as $k =>  $v) {
        //         $material_price += $v['khs_list_material_total'];
        //         $service_price += $v['khs_list_service_total'];
        //     }
        //     $return['khs']['project_id'] = $project_id;
        //     if ($khs_source == "TA") {
        //         $return['khs']['khs_service_total'] = $service_price;
        //     } else {
        //         $return['khs']['khs_material_total'] = $material_price;
        //         $return['khs']['khs_service_total'] = $service_price;
        //     }
        // }
        // if (
        //     $getData['project_status'] == 'Instalation' ||
        //     $getData['project_status'] == 'Approved Instalation' ||
        //     $getData['project_status'] == 'Termination' ||
        //     $getData['project_status'] == 'Valid 3' ||
        //     $getData['project_status'] == 'Labeling' ||
        //     $getData['project_status'] == 'Valid 4' ||
        //     $getData['project_status'] == 'Reconsiliasi' ||
        //     $getData['project_status'] == 'Pemberkasan' ||
        //     $getData['project_status'] == 'Submit' ||
        //     $getData['project_status'] == 'Paid'
        // ) {
        //     $getDataUser = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        //     if ($getDataUser == NULL) {
        //         $res = formatResponse(404, [], [], 'Data user not found', [], '');
        //         $this->response($res, 404);
        //     }
        //     $dataKHSList = [];
        //     $getDataKHSList = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'project_id' => $project_id]);
        //     foreach ($getDataKHSList as $k => $v) {
        //         $vv = $this->db
        //             ->join('product p', 'p.product_id=d.product_id')
        //             ->join('brand b', 'b.brand_id=p.brand_id')
        //             ->get_where('designator d', ['d.deleteAt' => NULL, 'd.designator_id' => $v['designator_id']])
        //             ->row_array();
        //         $params = [
        //             'khs_list_id' => $v['khs_list_id'],
        //             'khs_list_qty' => $v['khs_list_qty'],
        //             'designator_id' => $vv['designator_id'],
        //             'designator_code' => $vv['designator_code'],
        //             'designator_desc' => $vv['designator_desc'],
        //             'product_name' => $vv['product_name'],
        //             'product_portion' => $vv['product_portion'],
        //             'brand_name' => $vv['brand_name'],
        //             'khs_list_material_price' => $v['khs_list_material_price'],
        //             'khs_list_service_price' => $v['khs_list_service_price'],
        //             'khs_list_material_total' => $v['khs_list_material_total'],
        //             'khs_list_service_total' => $v['khs_list_service_total']
        //         ];
        //         $dataKHSList[] = $params;
        //     }
        //     $return['khs']['khs_list'] = $dataKHSList;
        //     //sampe sini

        //     $getDataKHS = $this->GlobalModel->getData('project_khs', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        //     $material_price = 0;
        //     $service_price = 0;
        //     foreach ($dataKHSList as $k =>  $v) {
        //         $material_price += $v['khs_list_material_total'];
        //         $service_price += $v['khs_list_service_total'];
        //     }
        //     $return['khs']['project_id'] = $project_id;
        //     if ($getDataKHS['khs_source'] == "TA") {
        //         $return['khs']['khs_service_total'] = $service_price;
        //     } else {
        //         $return['khs']['khs_material_total'] = $material_price;
        //         $return['khs']['khs_service_total'] = $service_price;
        //     }
        // }
        $persen = 0;
        $jobs = $this->db->join('job j', 'j.job_id=pj.job_id')->where(['pj.deleteAt' => NULL, 'pj.project_id' => $project_id])->get('project_job pj')->result_array();
        $dataJobs = [];
        foreach ($jobs as $k => $v) {
            if ($v['date_start'] != NULL && $v['date_done'] != NULL) {
                $persen += $v['job_percent'];
                if ($v['est_date_done'] != NULL) {
                    if ($v['est_date_done'] < $v['date_done']) {
                        $telat = $this->getRange($v['est_date_done'], $v['date_done']);
                    } else {
                        $telat = 0;
                    }
                } else {
                    $telat = 0;
                }
                $dataJobs[] = [
                    'job_name' => $v['job_name'],
                    'date_start' => $v['date_start'],
                    'date_done' => $v['date_done'],
                    'day' => $this->getRange($v['date_start'], $v['date_done']),
                    'est_date_start' => $v['est_date_start'],
                    'est_date_done' => $v['est_date_start'],
                    'est_day' => $this->getRange($v['est_date_start'], $v['est_date_done']),
                    'late' => $telat
                ];
            } elseif ($v['date_start'] != NULL && $v['date_done'] == NULL) {
                $dataJobs[] = [
                    'job_name' => $v['job_name'],
                    'date_start' => $v['date_start'],
                    'date_done' => 'on progress',
                    'day' => $this->getRange($v['date_start'], date('Y-m-d')),
                    'est_date_start' => $v['est_date_start'],
                    'est_date_done' => $v['est_date_start'],
                    'est_day' => $this->getRange($v['est_date_start'], $v['est_date_done']),
                    'late' => 0
                ];
            }
        }
        $return['persen'] = $persen;
        $return['jobs'] = $dataJobs;
        $res = formatResponse(200, $return, [], '', [], '');
        $this->response($res, 200);
    }

    private function getRange($date1, $date2)
    {
        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        return $days;
    }

    public function job_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['RJOB', 'UJOB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            $job = $this->db
                ->select('j.job_id,j.job_name,j.job_percent,pj.est_date_start,pj.est_date_done')
                ->join('job j', 'j.job_id=pj.job_id')
                ->get_where('project_job pj', ['pj.deleteAt' => NULL, 'pj.project_id' => $project_id])
                ->result_array();
            $res = formatResponse(200, $job, [], '', [], '');
            $this->response($res, 200);
        }
    }

    public function job_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UJOB']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $data = array(
            'project_id' => $this->put('project_id'),
            'job_id' => $this->put('job_id'),
            'est_date_start' => $this->put('est_date_start'),
            'est_date_done' => $this->put('est_date_done'),
        );

        $make = $this->validator->make($data, [
            'project_id' => 'required',
            'job_id' => 'required',
            'est_date_start' => 'required',
            'est_date_done' => 'required',
        ]);

        $make->setAliases([
            'project_id' => 'Project',
            'job_id' => 'Job',
            'est_date_start' => 'Start Date',
            'est_date_done' => 'End Date',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $data['project_id']], false);
            if ($getDataProject == NULL) {
                $res = formatResponse(404, [], [], 'Data project not found', [], '');
                $this->response($res, 404);
            } else {
                if (
                    $getDataProject['project_status'] != 'Pending'
                ) {
                    $res = formatResponse(400, [], [], 'Can\'t update estimation date project', [], '');
                    $this->response($res, 400);
                }
            }
            $getDataJob = $this->GlobalModel->getData('project_job', ['deleteAt' => NULL, 'project_id' => $data['project_id'], 'job_id' => $data['job_id']], false);
            if ($getDataJob == NULL) {
                $res = formatResponse(404, [], [], 'Data job not found', [], '');
                $this->response($res, 404);
            }
            $up = $this->GlobalModel->update('project_job', ['est_date_start' => $data['est_date_start'], 'est_date_done' => $data['est_date_done']], ['deleteAt' => NULL, 'project_id' => $data['project_id'], 'job_id' => $data['job_id']]);
            if ($up) {
                $res = formatResponse(200, $data, [], '', [], 'Success to update estimation date');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update estimation date', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function add_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CPRO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->join('witel ww', 'ww.witel_id=w.witel_id')->get_where('witel_user w', ['ww.deleteAt' => NULL, 'w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email']])->row_array();
        if ($check == NULL) {
            $res = formatResponse(400, [], [], 'You\'re don\'t have a witel', [], '');
            $this->response($res, 400);
        }
        $data = array(
            'project_code' => $this->post('project_code'),
            'project_date' => $this->post('project_date'),
            'project_note' => $this->post('project_note'),
            'project_start' => $this->post('project_start'),
            'project_done' => $this->post('project_done'),
            'cat_id' => $this->post('cat_id'),
            'witel_id' => $check['witel_id'],
            'project_status' => 'Pending',
        );

        $make = $this->validator->make($data, [
            'project_code' => 'required',
            'project_date' => 'required',
            'project_start' => 'required',
            'project_done' => 'required',
            'cat_id' => 'required',
            'witel_id' => 'required',
        ]);

        $make->setAliases([
            'project_code' => 'Project Code',
            'project_date' => 'Project Date',
            'project_note' => 'Project Note',
            'project_start' => 'Project Start',
            'project_done' => 'Project Done',
            'cat_id' => 'Category',
            'witel_id' => 'Witel',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('project_cat', ['cat_id' => $data['cat_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data category project not found', [], '');
                $this->response($res, 400);
            }
            if ($getData['cat_name'] == 'HEM' || $getData['cat_name'] == 'NOD-B') {
                $data['label_cat'] = 1;
            } else {
                $data['label_cat'] = 0;
            }
            $this->db->trans_begin();
            $this->GlobalModel->insert('project', $data);
            $id = $this->db->insert_id();
            if ($this->db->trans_status() == FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed to add project', [], '');
                $this->response($res, 400);
            }
            $job = $this->GlobalModel->getData('job', ['deleteAt' => NULL]);
            if ($this->db->trans_status() == FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed to add project', [], '');
                $this->response($res, 400);
            }
            $params = [];
            foreach ($job as $k => $v) {
                $params[] = [
                    'job_id' => $v['job_id'],
                    'project_id' => $id
                ];
            }
            $this->db->insert_batch('project_job', $params);
            if ($this->db->trans_status() == FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed to add project', [], '');
                $this->response($res, 400);
            }
            $this->db->trans_commit();
            $folder = 'DATA-PROJECT-' . $id;
            $folder = './assets/project/' . filename_safe($folder);
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
                if (is_dir($folder)) {
                    mkdir($folder . '/survey', 0777, true);
                    mkdir($folder . '/instalasi', 0777, true);
                    mkdir($folder . '/terminasi', 0777, true);
                    mkdir($folder . '/labeling', 0777, true);
                    mkdir($folder . '/deleted', 0777, true);
                    if (is_dir($folder . '/deleted')) {
                        mkdir($folder . '/deleted' . '/survey', 0777, true);
                        mkdir($folder . '/deleted' . '/instalasi', 0777, true);
                        mkdir($folder . '/deleted' . '/terminasi', 0777, true);
                        mkdir($folder . '/deleted' . '/labeling', 0777, true);
                    }
                }
            }
            $data = $this->GlobalModel->getData('project', ['project_id' => $id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], 'Success to create project');
            $this->response($res, 200);
        }
    }

    public function edit_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UPRO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if (
                $getData['project_status'] == 'Approve' ||
                $getData['project_status'] == 'Survey' ||
                $getData['project_status'] == 'KHS Check' ||
                $getData['project_status'] == 'Instalation' ||
                $getData['project_status'] == 'Terminasi' ||
                $getData['project_status'] == 'Valid 3' ||
                $getData['project_status'] == 'Labeling' ||
                $getData['project_status'] == 'Valid 4' ||
                $getData['project_status'] == 'Reconsiliasi'
            ) {
                $res = formatResponse(400, [], [], 'Can\'t edit project', [], '');
                $this->response($res, 400);
            }
        }
        $check = $this->db->select('w.witel_id')->join('user u', 'u.userCode=w.userCode')->join('witel ww', 'ww.witel_id=w.witel_id')->get_where('witel_user w', ['ww.deleteAt' => NULL, 'w.deleteAt' => NULL, 'u.email' => $this->payload['data']['email']])->row_array();
        if ($check == NULL) {
            $res = formatResponse(400, [], [], 'You\'re don\'t have a witel', [], '');
            $this->response($res, 400);
        }
        $data = array(
            'project_code' => $this->put('project_code'),
            'project_date' => $this->put('project_date'),
            'project_note' => $this->put('project_note'),
            'project_start' => $this->put('project_start'),
            'project_done' => $this->put('project_done'),
            'cat_id' => $this->put('cat_id'),
            'witel_id' => $check['witel_id'],
        );

        $make = $this->validator->make($data, [
            'project_code' => 'required',
            'project_date' => 'required',
            'project_start' => 'required',
            'project_done' => 'required',
            'cat_id' => 'required',
            'witel_id' => 'required',
        ]);

        $make->setAliases([
            'project_code' => 'Project Code',
            'project_date' => 'Project Date',
            'project_note' => 'Project Note',
            'project_start' => 'Project Start',
            'project_done' => 'Project Done',
            'cat_id' => 'Category',
            'witel_id' => 'Witel',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $getData = $this->GlobalModel->getData('project_cat', ['cat_id' => $data['cat_id'], 'deleteAt' => NULL], false);
            if ($getData == NULL) {
                $res = formatResponse(400, [], [], 'Data category project not found', [], '');
                $this->response($res, 400);
            }
            $cek = $this->GlobalModel->update('project', $data, ['project_id' => $project_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('project', ['project_id' => $project_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update project');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update project', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function delete_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DPRO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if (
                $getData['project_status'] == 'Approve' ||
                $getData['project_status'] == 'Survey' ||
                $getData['project_status'] == 'KHS Check' ||
                $getData['project_status'] == 'Instalation' ||
                $getData['project_status'] == 'Terminasi' ||
                $getData['project_status'] == 'Valid 3' ||
                $getData['project_status'] == 'Labeling' ||
                $getData['project_status'] == 'Valid 4' ||
                $getData['project_status'] == 'Reconsiliasi'
            ) {
                $res = formatResponse(400, [], [], 'Can\'t delete project', [], '');
                $this->response($res, 400);
            }
        }
        $getFeeder = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);
        foreach ($getFeeder as $k => $v) {
            $this->GlobalModel->delete('project_distribusi', ['deleteAt' => NULL, 'feeder_id' => $v['feeder_id']]);
        }
        $this->GlobalModel->delete('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);

        $cek = $this->GlobalModel->delete('project', ['project_id' => $project_id]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete project');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to delete project', [], '');
            $this->response($res, 400);
        }
    }

    public function toApprove_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['APRO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if (
                $getData['project_status'] == 'Approve' ||
                $getData['project_status'] == 'Survey' ||
                $getData['project_status'] == 'KHS Check' ||
                $getData['project_status'] == 'Approved Instalation' ||
                $getData['project_status'] == 'Instalation' ||
                $getData['project_status'] == 'Terminasi' ||
                $getData['project_status'] == 'Valid 3' ||
                $getData['project_status'] == 'Labeling' ||
                $getData['project_status'] == 'Valid 4' ||
                $getData['project_status'] == 'Reconsiliasi'
            ) {
                $res = formatResponse(400, [], [], 'Can\'t change status this project', [], '');
                $this->response($res, 400);
            }
        }
        $data = [
            'project_status' => 'Approve'
        ];
        $cek = $this->GlobalModel->update('project', $data, ['project_id' => $project_id]);
        if ($cek) {
            $data = $this->GlobalModel->getData('project', ['project_id' => $project_id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], 'Success to change status project');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to change status project', [], '');
            $this->response($res, 400);
        }
    }

    public function toDecline_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DEPRO']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if (
                $getData['project_status'] == 'Approve' ||
                $getData['project_status'] == 'Survey' ||
                $getData['project_status'] == 'KHS Check' ||
                $getData['project_status'] == 'Instalation' ||
                $getData['project_status'] == 'Terminasi' ||
                $getData['project_status'] == 'Valid 3' ||
                $getData['project_status'] == 'Labeling' ||
                $getData['project_status'] == 'Valid 4' ||
                $getData['project_status'] == 'Reconsiliasi'
            ) {
                $res = formatResponse(400, [], [], 'Can\'t change status this project', [], '');
                $this->response($res, 400);
            }
        }
        $data = [
            'project_status' => 'Decline'
        ];
        $cek = $this->GlobalModel->update('project', $data, ['project_id' => $project_id]);
        if ($cek) {
            $data = $this->GlobalModel->getData('project', ['project_id' => $project_id, 'deleteAt' => NULL], false);
            $res = formatResponse(200, $data, [], '', [], 'Success to change status project');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to change status project', [], '');
            $this->response($res, 400);
        }
    }

    private $postTeknisiFormat = [
        [
            'userCode' => 1,
            'user_leader' => 0
        ],
        [
            'userCode' => 2,
            'user_leader' => 1
        ]
    ];

    public function addTechnician_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CTEC']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if (
                $getData['project_status'] == 'Pending' ||
                $getData['project_status'] == 'Decline'
            ) {
                $res = formatResponse(400, [], [], 'Can\'t add technician for this project', [], '');
                $this->response($res, 400);
            }
        }
        if (!json_decode($this->post('technician'), true)) {
            $res = formatResponse(400, [], [], 'Wrong format for add technician', [], '');
            $this->response($res, 400);
        }
        $data = array(
            'technician' => json_decode($this->post('technician'), true),
            'status' => $this->post('status'),
        );

        $make = $this->validator->make($data, [
            'technician' => 'array',
            'technician.*.userCode' => 'required',
            'technician.*.user_leader' => 'required|integer',
            'status' => 'required|in:Approve,Survey'
        ]);

        $make->setAliases([
            'technician.*.userCode' => 'User',
            'technician.*.user_leader' => 'Leader',
            'status' => 'Status'
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $params = [];
            $status = 0;
            foreach ($data['technician'] as $k => $v) {
                if ($v['user_leader'] == 1) {
                    $status += 1;
                }
                $check = $this->GlobalModel->getData('user', ['deleteAt' => NULL, 'userCode' => $v['userCode']], false);
                if ($check == NULL) {
                    $res = formatResponse(400, [], [], 'User not found', [], '');
                    $this->response($res, 400);
                }
                $params[] = [
                    'project_id' => $project_id,
                    'userCode' => $v['userCode'],
                    'user_leader' => $v['user_leader']
                ];
            }
            if ($status > 1) {
                $res = formatResponse(400, [], [], 'Can\'t choose many leaders', [], '');
                $this->response($res, 400);
            }
            if ($status == 0) {
                $res = formatResponse(400, [], [], 'Must choose a leader', [], '');
                $this->response($res, 400);
            }
            $cek = $this->db->insert_batch('project_user', $params);
            if ($cek) {
                $this->GlobalModel->update('project', ['project_status' => $data['status']], ['project_id' => $project_id]);
                $this->recordJob(1, $project_id, date('Y-m-d'));
                $res = formatResponse(200, [], [], '', [], 'Success add technician and set leader');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed add technician and set leader', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function updateTechnician_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UTEC']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Pending' || $getData['project_status'] == 'Decline') {
                $res = formatResponse(400, [], [], 'Can\'t edit project', [], '');
                $this->response($res, 400);
            }
        }
        if (!json_decode($this->post('technician'), true)) {
            $res = formatResponse(400, [], [], 'Wrong format for add technician', [], '');
            $this->response($res, 400);
        }
        $data = array(
            'technician' => json_decode($this->post('technician'), true),
            'status' => $this->post('status'),
        );

        $make = $this->validator->make($data, [
            'technician' => 'array',
            'technician.*.userCode' => 'required',
            'technician.*.user_leader' => 'required|integer'
        ]);

        $make->setAliases([
            'technician.*.userCode' => 'User',
            'technician.*.user_leader' => 'Leader',
            'status' => 'Status',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $params = [];
            $status = 0;
            foreach ($data['technician'] as $k => $v) {
                if ($v['user_leader'] == 1) {
                    $status += 1;
                }
                $check = $this->GlobalModel->getData('user', ['deleteAt' => NULL, 'userCode' => $v['userCode']], false);
                if ($check == NULL) {
                    $res = formatResponse(400, [], [], 'User not found', [], '');
                    $this->response($res, 400);
                }
                $params[] = [
                    'project_id' => $project_id,
                    'userCode' => $v['userCode'],
                    'user_leader' => $v['user_leader']
                ];
            }
            if ($status > 1) {
                $res = formatResponse(400, [], [], 'Can\'t choose many leaders', [], '');
                $this->response($res, 400);
            }
            if ($status == 0) {
                $res = formatResponse(400, [], [], 'Must choose a leader', [], '');
                $this->response($res, 400);
            }
            $this->db->trans_begin();
            $this->GlobalModel->delete('project_user', ['project_id' => $project_id]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed update technician and set leader', [], '');
                $this->response($res, 400);
            }
            $this->db->insert_batch('project_user', $params);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed update technician and set leader', [], '');
                $this->response($res, 400);
            }
            if ($data['status'] != NULL) {
                $this->GlobalModel->update('project', ['project_status' => $data['status']], ['project_id' => $project_id]);
            }
            $this->db->trans_commit();
            $res = formatResponse(200, [], [], '', [], 'Success update technician and set leader');
            $this->response($res, 200);
        }
    }

    // sitax_type = sitax | non-sitax
    // sitax_list = ["rt","rw"]

    public function sitax_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CUSI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t add sitax because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'sitax_type' => $this->post('sitax_type'),
        );

        $make = $this->validator->make($data, [
            'sitax_type' => 'required|in:sitax,non-sitax',
        ]);

        $make->setAliases([
            'sitax_type' => 'Type sitax',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $check = $this->GlobalModel->getData('project_sitax', ['project_id' => $project_id], false);
            if ($check == NULL) {
                if ($data['sitax_type'] == 'non-sitax') {
                    $params = [
                        'project_id' => $project_id,
                        'sitax_list' => NULL,
                        'sitax_total' => 0
                    ];
                    $in = $this->db->insert('project_sitax', $params);
                    if ($in) {
                        $res = formatResponse(200, [], [], '', [], 'Success add sitax');
                        $this->response($res, 200);
                    } else {
                        $res = formatResponse(400, [], [], 'Failed add sitax', [], '');
                        $this->response($res, 400);
                    }
                }
                if (!json_decode($this->post('sitax_list'), true)) {
                    $res = formatResponse(400, [], [], 'Wrong format for add sitax', [], '');
                    $this->response($res, 400);
                }
                $data = array(
                    'sitax_list' => json_decode($this->post('sitax_list'), true),
                    'sitax_total' => $this->post('sitax_total')
                );

                $make = $this->validator->make($data, [
                    'sitax_list' => 'array',
                    'sitax_list.*' => 'required',
                    'sitax_total' => 'required',
                ]);

                $make->setAliases([
                    'sitax_list.*' => 'Sitax',
                    'sitax_total' => 'Total',
                ]);

                $make->validate();

                if ($make->fails()) {
                    $errors = $make->errors();
                    $sitax_list = (isset($errors->firstOfAll()['sitax_list'])) ? array_values($errors->firstOfAll()['sitax_list'])[0] : '';
                    if ($sitax_list != '') {
                        $err['sitax_list'] = $sitax_list;
                    }
                    $sitax_total = (isset($errors->firstOfAll()['sitax_total'])) ? $errors->firstOfAll()['sitax_total'] : '';
                    if ($sitax_total != '') {
                        $err['sitax_total'] = $sitax_total;
                    }
                    $res = formatResponse(400, [], $err, '', [], '');
                    $this->response($res, 400);
                } else {
                    $params = [
                        'project_id' => $project_id,
                        'sitax_list' => json_encode($data['sitax_list'], true),
                        'sitax_total' => $data['sitax_total']
                    ];
                    $in = $this->db->insert('project_sitax', $params);
                    if ($in) {
                        $res = formatResponse(200, [], [], '', [], 'Success add sitax');
                        $this->response($res, 200);
                    } else {
                        $res = formatResponse(400, [], [], 'Failed add sitax', [], '');
                        $this->response($res, 400);
                    }
                }
            } else {
                if ($data['sitax_type'] == 'non-sitax') {
                    $params = [
                        'project_id' => $project_id,
                        'sitax_list' => NULL,
                        'sitax_total' => 0
                    ];
                    $in = $this->GlobalModel->update('project_sitax', $params, ['sitax_id' => $check['sitax_id']]);
                    if ($in) {
                        $res = formatResponse(200, [], [], '', [], 'Success update sitax');
                        $this->response($res, 200);
                    } else {
                        $res = formatResponse(400, [], [], 'Failed update sitax', [], '');
                        $this->response($res, 400);
                    }
                }
                if (!json_decode($this->post('sitax_list'), true)) {
                    $res = formatResponse(400, [], [], 'Wrong format for update sitax', [], '');
                    $this->response($res, 400);
                }
                $data = array(
                    'sitax_list' => json_decode($this->post('sitax_list'), true),
                    'sitax_total' => $this->post('sitax_total')
                );

                $make = $this->validator->make($data, [
                    'sitax_list' => 'array',
                    'sitax_list.*' => 'required',
                    'sitax_total' => 'required',
                ]);

                $make->setAliases([
                    'sitax_list.*' => 'Sitax',
                    'sitax_total' => 'Total',
                ]);

                $make->validate();

                if ($make->fails()) {
                    $errors = $make->errors();
                    $sitax_list = (isset($errors->firstOfAll()['sitax_list'])) ? array_values($errors->firstOfAll()['sitax_list'])[0] : '';
                    if ($sitax_list != '') {
                        $err['sitax_list'] = $sitax_list;
                    }
                    $sitax_total = (isset($errors->firstOfAll()['sitax_total'])) ? $errors->firstOfAll()['sitax_total'] : '';
                    if ($sitax_total != '') {
                        $err['sitax_total'] = $sitax_total;
                    }
                    $res = formatResponse(400, [], $err, '', [], '');
                    $this->response($res, 400);
                } else {
                    $params = [
                        'project_id' => $project_id,
                        'sitax_list' => json_encode($data['sitax_list'], true),
                        'sitax_total' => $data['sitax_total']
                    ];
                    $in = $this->GlobalModel->update('project_sitax', $params, ['sitax_id' => $check['sitax_id']]);
                    if ($in) {
                        $res = formatResponse(200, [], [], '', [], 'Success update sitax');
                        $this->response($res, 200);
                    } else {
                        $res = formatResponse(400, [], [], 'Failed update sitax', [], '');
                        $this->response($res, 400);
                    }
                }
            }
        }
    }

    //add data teknis
    public $format_data_teknis = [
        [
            'project_id' => 1,
            'khs_list' => [
                [
                    'tipe' => 'GPON', // Feeder|Penggelaran|ODP|ODC|GPON
                    'designator_id' => 1,
                    'khs_list_qty' => 30,
                    // jika tipe GPON
                    'GPON' => [
                        'gpon' => 30,
                        'slot' => 30,
                        'port' => 30,
                        'output_feeder' => 30,
                        'output_pasif' => 30,
                    ]
                ]
            ]
        ],
        [
            'project_id' => 1,
            'khs_list' => [
                [
                    'tipe' => 'Feeder', // Feeder|Penggelaran|ODP|ODC|GPON
                    'designator_id' => 1,
                    'khs_list_qty' => 30
                ]
            ]
        ]
    ];

    public function addDataTeknis_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CFED']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }

        if (!json_decode($this->post('data_teknis'), true)) {
            $res = formatResponse(400, [], [], 'Wrong format for add data teknis', [], '');
            $this->response($res, 400);
        }

        $data = array(
            'data_teknis' => json_decode($this->post('data_teknis'), true)
        );

        $make = $this->validator->make($data, [
            'data_teknis' => 'array',
            'data_teknis.*.project_id' => 'required',
            'data_teknis.*.khs_list' => 'array|required',
        ]);

        $make->setAliases([
            'data_teknis.*.project_id' => 'Project',
            'data_teknis.*.khs_list' => 'KHS',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->db->trans_begin();
            foreach ($data['data_teknis'] as $k => $v) {
                $project_id = $v['project_id'];
                if ($project_id == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'ID project is required', [], '');
                    $this->response($res, 400);
                }
                $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
                if ($getData == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(404, [], [], 'Data project not found', [], '');
                    $this->response($res, 404);
                } else {
                    if ($getData['project_status'] != 'Survey') {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Can\'t add feeder because status project not a \'Survey\'', [], '');
                        $this->response($res, 400);
                    }
                }
                $paramsKHS = [
                    'project_id' => $v['project_id'],
                ];
                $insertKHS = $this->db->insert('project_khs', $paramsKHS);
                if ($insertKHS) {
                    $khs_id = $this->db->insert_id();
                    if (isset($v['khs_list'])) {
                        foreach ($v['khs_list'] as $k => $t) {
                            if ($t['tipe'] == 'Feeder') {
                                $insertFeeder = $this->db->insert('project_feeder', [
                                    'createAt' => date('Y-m-d H:i:s')
                                ]);
                                if (!$insertFeeder) {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                    $this->response($res, 400);
                                }
                                $tipe_id = $this->db->insert_id();
                            } elseif ($t['tipe'] == 'Penggelaran') {
                                $insertPenggelaran = $this->db->insert('project_penggelaran', [
                                    'createAt' => date('Y-m-d H:i:s')
                                ]);
                                if (!$insertPenggelaran) {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                    $this->response($res, 400);
                                }
                                $tipe_id = $this->db->insert_id();
                            } elseif ($t['tipe'] == 'ODP') {
                                if (isset($t['ODP']) && $t['ODP'] != NULL) {
                                    $make2 = $this->validator->make($t['ODP'], [
                                        'address' => 'required',
                                        'lg' => 'required',
                                        'lt' => 'required',
                                        'benchmark_address' => 'required',
                                        'core' => 'required',
                                        'distribusi_core' => 'required',
                                    ]);

                                    $make2->setAliases([
                                        'address' => 'Alamat',
                                        'lg' => 'Longitude',
                                        'lt' => 'Latitude',
                                        'benchmark_address' => 'Patokan',
                                        'core' => 'Core',
                                        'distribusi_core' => 'Core distribusi',
                                    ]);

                                    $make2->validate();

                                    if ($make2->fails()) {
                                        $this->db->trans_rollback();
                                        $errors = $make2->errors();
                                        $err = $errors->firstOfAll();
                                        $res = formatResponse(400, [], $err, '', [], '');
                                        $this->response($res, 400);
                                    }
                                    $insertODP = $this->db->insert('project_odp', [
                                        'address' => $t['ODP']['address'],
                                        'lg' => $t['ODP']['lg'],
                                        'lt' => $t['ODP']['lt'],
                                        'benchmark_address' => $t['ODP']['benchmark_address'],
                                        'core' => $t['ODP']['core'],
                                        'core_opsi' => (isset($t['ODP']['core_opsi'])) ? $t['ODP']['core_opsi'] : NULL,
                                        'distribusi_core' => $t['ODP']['distribusi_core'],
                                        'distribusi_core_opsi' => (isset($t['ODP']['distribusi_core_opsi'])) ? $t['ODP']['distribusi_core_opsi'] : NULL,
                                        'createAt' => date('Y-m-d H:i:s')
                                    ]);
                                    if (!$insertODP) {
                                        $this->db->trans_rollback();
                                        $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                        $this->response($res, 400);
                                    }
                                    $tipe_id = $this->db->insert_id();
                                } else {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Data ODP can\'t empty', [], '');
                                    $this->response($res, 400);
                                }
                            } elseif ($t['tipe'] == 'ODC') {
                                if (isset($t['ODC']) && $t['ODC'] != NULL) {
                                    $make2 = $this->validator->make($t['ODC'], [
                                        'address' => 'required',
                                        'lg' => 'required',
                                        'lt' => 'required',
                                        'benchmark_address' => 'required',
                                    ]);

                                    $make2->setAliases([
                                        'address' => 'Alamat',
                                        'lg' => 'Longitude',
                                        'lt' => 'Latitude',
                                        'benchmark_address' => 'Patokan',
                                    ]);

                                    $make2->validate();

                                    if ($make2->fails()) {
                                        $this->db->trans_rollback();
                                        $errors = $make2->errors();
                                        $err = $errors->firstOfAll();
                                        $res = formatResponse(400, [], $err, '', [], '');
                                        $this->response($res, 400);
                                    }
                                    $insertODC = $this->db->insert('project_odc', [
                                        'address' => $t['ODC']['address'],
                                        'lg' => $t['ODC']['lg'],
                                        'lt' => $t['ODC']['lt'],
                                        'benchmark_address' => $t['ODC']['benchmark_address'],
                                        'createAt' => date('Y-m-d H:i:s')
                                    ]);
                                    if (!$insertODC) {
                                        $this->db->trans_rollback();
                                        $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                        $this->response($res, 400);
                                    }
                                    $tipe_id = $this->db->insert_id();
                                } else {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Data ODC can\'t empty', [], '');
                                    $this->response($res, 400);
                                }
                            } elseif ($t['tipe'] == 'GPON') {
                                if (isset($t['GPON']) && $t['GPON'] != NULL) {
                                    $make2 = $this->validator->make($t['GPON'], [
                                        'gpon' => 'required|integer',
                                        'slot' => 'required|integer',
                                        'port' => 'required|integer',
                                        'output_feeder' => 'required|numeric',
                                        'output_pasif' => 'required|numeric',
                                    ]);

                                    $make2->setAliases([
                                        'gpon' => 'GPON',
                                        'slot' => 'Slot',
                                        'port' => 'Port',
                                        'output_feeder' => 'Feeder',
                                        'output_pasif' => 'Pasif',
                                    ]);

                                    $make2->validate();

                                    if ($make2->fails()) {
                                        $this->db->trans_rollback();
                                        $errors = $make2->errors();
                                        $err = $errors->firstOfAll();
                                        $res = formatResponse(400, [], $err, '', [], '');
                                        $this->response($res, 400);
                                    }
                                    $insertGPON = $this->db->insert('project_gpon', [
                                        'gpon' => $t['GPON']['gpon'],
                                        'slot' => $t['GPON']['slot'],
                                        'port' => $t['GPON']['port'],
                                        'output_feeder' => $t['GPON']['output_feeder'],
                                        'output_pasif' => $t['GPON']['output_pasif'],
                                        'createAt' => date('Y-m-d H:i:s')
                                    ]);
                                    if (!$insertGPON) {
                                        $this->db->trans_rollback();
                                        $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                        $this->response($res, 400);
                                    }
                                    $tipe_id = $this->db->insert_id();
                                } else {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                    $this->response($res, 400);
                                }
                            } else {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Data GPON can\'t empty', [], '');
                                $this->response($res, 400);
                            }
                            $paramsKHSList = [
                                'tipe' => $t['tipe'],
                                'tipe_id' => $tipe_id,
                                'khs_id' => $khs_id,
                                'designator_id' => $t['designator_id'],
                                'khs_list_qty' => $t['khs_list_qty'],
                            ];
                            $insertKHSList = $this->db->insert('project_khs_list', $paramsKHSList);
                            if (!$insertKHSList) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                                $this->response($res, 400);
                            }
                        }
                    } else {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                        $this->response($res, 400);
                    }
                } else {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Failed to add data teknis', [], '');
                    $this->response($res, 400);
                }
            }
            $this->db->trans_commit();
            $res = formatResponse(200, [], [], '', [], 'Success to add data teknis');
            $this->response($res, 200);
        }
    }

    public function deleteDataTeknis_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DDIS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $khs_id = $this->get('id');
        if ($khs_id == NULL) {
            $res = formatResponse(400, [], [], 'ID khs is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project_khs', ['deleteAt' => NULL, 'khs_id' => $khs_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data khs not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getData['project_id']], false);
            if ($getDataProject == NULL) {
                $res = formatResponse(404, [], [], 'Data project not found', [], '');
                $this->response($res, 404);
            } else {
                if ($getDataProject['project_status'] != 'Survey') {
                    $res = formatResponse(400, [], [], 'Can\'t delete khs because status project not a \'Survey\'', [], '');
                    $this->response($res, 400);
                }
            }
        }
        $in = $this->GlobalModel->delete('project_khs', ['deleteAt' => NULL, 'khs_id' => $khs_id]);
        if ($in) {
            $res = formatResponse(200, [], [], '', [], 'Success delete khs');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete khs', [], '');
            $this->response($res, 400);
        }
    }

    public function deleteDataTeknisList_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DDIS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $list_khs_id = $this->get('id');
        if ($list_khs_id == NULL) {
            $res = formatResponse(400, [], [], 'ID list khs is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'khs_list_id' => $list_khs_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data list khs not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataKHS = $this->GlobalModel->getData('project_khs', ['deleteAt' => NULL, 'khs_id' => $getData['khs_id']], false);
            if ($getDataKHS == NULL) {
                $res = formatResponse(404, [], [], 'Data KHS not found', [], '');
                $this->response($res, 404);
            } else {
                $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getDataKHS['project_id']], false);
                if ($getDataProject == NULL) {
                    $res = formatResponse(404, [], [], 'Data project not found', [], '');
                    $this->response($res, 404);
                } else {
                    if ($getDataProject['project_status'] != 'Survey') {
                        $res = formatResponse(400, [], [], 'Can\'t delete khs because status project not a \'Survey\'', [], '');
                        $this->response($res, 400);
                    }
                }
            }
        }
        $in = $this->GlobalModel->delete('project_khs_list', ['deleteAt' => NULL, 'khs_list_id' => $list_khs_id]);
        if ($in) {
            $res = formatResponse(200, [], [], '', [], 'Success delete khs list');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete khs list', [], '');
            $this->response($res, 400);
        }
    }

    private $postDetailFormat = [
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
            ]
        ]
    ];

    public function addFeederBatch_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CFED']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }

        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t add feeder because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        if (!json_decode($this->post('feeder'), true)) {
            $res = formatResponse(400, [], [], 'Wrong format for add feeder', [], '');
            $this->response($res, 400);
        }

        $data = array(
            'feeder' => json_decode($this->post('feeder'), true)
        );

        $make = $this->validator->make($data, [
            'feeder' => 'array',
            'feeder.*.feeder_odc' => 'required',
            'feeder.*.feeder_capacity' => 'required',
            'feeder.*.feeder_address' => 'required',
            'feeder.*.feeder_lg' => 'required',
            'feeder.*.feeder_lt' => 'required',
            'feeder.*.feeder_port' => 'required|integer|min:1|max:100',
            'feeder.*.feeder_core' => 'required|integer|min:1|max:288',
        ]);

        $make->setAliases([
            'feeder.*.feeder_odc' => 'ODC',
            'feeder.*.feeder_capacity' => 'Capacity',
            'feeder.*.feeder_address' => 'Address',
            'feeder.*.feeder_lg' => 'Longitude',
            'feeder.*.feeder_lt' => 'Latitude',
            'feeder.*.feeder_port' => 'Port',
            'feeder.*.feeder_core' => 'Core',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $params = [];
            foreach ($data['feeder'] as $k => $v) {
                $params[] = [
                    'project_id' => $project_id,
                    'feeder_odc' => $v['feeder_odc'],
                    'feeder_capacity' => $v['feeder_capacity'],
                    'feeder_address' => $v['feeder_address'],
                    'feeder_lg' => $v['feeder_lg'],
                    'feeder_lt' => $v['feeder_lt'],
                    'feeder_port' => $v['feeder_port'],
                    'feeder_core' => $v['feeder_core'],
                ];
            }
            $in = $this->db->insert_batch('project_feeder', $params);
            if ($in) {
                $res = formatResponse(200, [], [], '', [], 'Success add feeder');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed add feeder', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function updateFeeder_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UFED']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $feeder_id = $this->get('id');
        if ($feeder_id == NULL) {
            $res = formatResponse(400, [], [], 'ID feeder is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $feeder_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data feeder not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getData['project_id']], false);
            if ($getDataProject == NULL) {
                $res = formatResponse(404, [], [], 'Data project not found', [], '');
                $this->response($res, 404);
            } else {
                if ($getDataProject['project_status'] != 'Survey') {
                    $res = formatResponse(400, [], [], 'Can\'t edit feeder because status project not a \'Survey\'', [], '');
                    $this->response($res, 400);
                }
            }
        }

        $data = array(
            'feeder_odc' => $this->put('feeder_odc'),
            'feeder_capacity' => $this->put('feeder_capacity'),
            'feeder_address' => $this->put('feeder_address'),
            'feeder_lg' => $this->put('feeder_lg'),
            'feeder_lt' => $this->put('feeder_lt'),
            'feeder_port' => $this->put('feeder_port'),
            'feeder_core' => $this->put('feeder_core'),
        );

        $make = $this->validator->make($data, [
            'feeder_odc' => 'required',
            'feeder_capacity' => 'required',
            'feeder_address' => 'required',
            'feeder_lg' => 'required',
            'feeder_lt' => 'required',
            'feeder_port' => 'required|integer|min:1|max:100',
            'feeder_core' => 'required|integer|min:1|max:288',
        ]);

        $make->setAliases([
            'feeder_odc' => 'ODC',
            'feeder_capacity' => 'Capacity',
            'feeder_address' => 'Address',
            'feeder_lg' => 'Longitude',
            'feeder_lt' => 'Latitude',
            'feeder_port' => 'Port',
            'feeder_core' => 'Core',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $in = $this->GlobalModel->update('project_feeder', $data, ['deleteAt' => NULL, 'feeder_id' => $feeder_id]);
            if ($in) {
                $res = formatResponse(200, [], [], '', [], 'Success update feeder');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed update feeder', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteFeeder_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DFED']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $feeder_id = $this->get('id');
        if ($feeder_id == NULL) {
            $res = formatResponse(400, [], [], 'ID feeder is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $feeder_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data feeder not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getData['project_id']], false);
            if ($getDataProject == NULL) {
                $res = formatResponse(404, [], [], 'Data project not found', [], '');
                $this->response($res, 404);
            } else {
                if ($getDataProject['project_status'] != 'Survey') {
                    $res = formatResponse(400, [], [], 'Can\'t delete feeder because status project not a \'Survey\'', [], '');
                    $this->response($res, 400);
                }
            }
        }
        $in = $this->GlobalModel->delete('project_distribusi', ['deleteAt' => NULL, 'feeder_id' => $feeder_id]);

        $in = $this->GlobalModel->delete('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $feeder_id]);
        if ($in) {
            $res = formatResponse(200, [], [], '', [], 'Success delete feeder');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete feeder', [], '');
            $this->response($res, 400);
        }
    }

    public function addDistribusiBatch_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CDIS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $feeder_id = $this->get('id');
        if ($feeder_id == NULL) {
            $res = formatResponse(400, [], [], 'ID feeder is required', [], '');
            $this->response($res, 400);
        }

        $getDataFeeder = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $feeder_id], false);
        if ($getDataFeeder == NULL) {
            $res = formatResponse(404, [], [], 'Data feeder not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getDataFeeder['project_id']], false);
            if ($getDataProject == NULL) {
                $res = formatResponse(404, [], [], 'Data project not found', [], '');
                $this->response($res, 404);
            } else {
                if ($getDataProject['project_status'] != 'Survey') {
                    $res = formatResponse(400, [], [], 'Can\'t add distribusi because status project not a \'Survey\'', [], '');
                    $this->response($res, 400);
                }
            }
        }

        if (!json_decode($this->post('distribusi'), true)) {
            $res = formatResponse(400, [], [], 'Wrong format for distribution', [], '');
            $this->response($res, 400);
        }

        $data = array(
            'distribusi' => json_decode($this->post('distribusi'), true)
        );

        $val = [
            'distribusi' => 'array',
            'distribusi.*.distribusi_kukd' => 'required|in:12,24,48',
            'distribusi.*.distribusi_address' => 'required',
            'distribusi.*.distribusi_benchmark_address' => 'required',
            'distribusi.*.distribusi_odp' => 'required',
            'distribusi.*.distribusi_lg' => 'required',
            'distribusi.*.distribusi_lt' => 'required',
            'distribusi.*.distribusi_core' => 'required|min:1|max:48',
            'distribusi.*.distribusi_capacity' => 'required|in:8,16',
        ];

        if ($getDataProject['label_cat'] != '1') {
            $val['distribusi.*.distribusi_dropcore'] = 'required|integer';
        }

        $make = $this->validator->make($data, $val);

        $make->setAliases([
            'distribusi.*.distribusi_kukd' => 'KU/KD',
            'distribusi.*.distribusi_address' => 'Address',
            'distribusi.*.distribusi_benchmark_address' => 'Benchmark Address',
            'distribusi.*.distribusi_odp' => 'ODP',
            'distribusi.*.distribusi_lg' => 'Longitude',
            'distribusi.*.distribusi_lt' => 'Latitude',
            'distribusi.*.distribusi_core' => 'Core',
            'distribusi.*.distribusi_capacity' => 'Capacity',
            'distribusi.*.distribusi_dropcore' => 'Dropcore'
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $params = [];
            // $check = $this->GlobalModel->getData('project_distribusi', ['feeder_id' => $feeder_id, 'deleteAt' => NULL], false, 'distribusi_odp');
            // $odp = ($check == NULL) ? 0 : $check['distribusi_odp'];
            foreach ($data['distribusi'] as $k => $v) {
                $params[] = [
                    'feeder_id' => $feeder_id,
                    'distribusi_kukd' => $v['distribusi_kukd'],
                    'distribusi_odp' => $v['distribusi_odp'],
                    'distribusi_dropcore' => ($getDataProject['label_cat'] == '1') ? NULL : $v['distribusi_dropcore'],
                    'distribusi_address' => $v['distribusi_address'],
                    'distribusi_benchmark_address' => $v['distribusi_benchmark_address'],
                    'distribusi_lg' => $v['distribusi_lg'],
                    'distribusi_lt' => $v['distribusi_lt'],
                    'distribusi_core' => $v['distribusi_core'],
                    'distribusi_core_opsi' => $v['distribusi_core_opsi'],
                    'distribusi_capacity' => $v['distribusi_capacity'],
                    'distribusi_note' => $v['distribusi_note'],
                ];
            }
            $in = $this->db->insert_batch('project_distribusi', $params);
            if ($in) {
                $res = formatResponse(200, [], [], '', [], 'Success add distribusi');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed add distribusi', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function updateDistribusi_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UDIS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $distribusi_id = $this->get('id');
        if ($distribusi_id == NULL) {
            $res = formatResponse(400, [], [], 'ID distribusi is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $distribusi_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data distribusi not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataFeeder = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $getData['feeder_id']], false);
            if ($getDataFeeder == NULL) {
                $res = formatResponse(404, [], [], 'Data feeder not found', [], '');
                $this->response($res, 404);
            } else {
                $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getDataFeeder['project_id']], false);
                if ($getDataProject == NULL) {
                    $res = formatResponse(404, [], [], 'Data project not found', [], '');
                    $this->response($res, 404);
                } else {
                    if ($getDataProject['project_status'] != 'Survey') {
                        $res = formatResponse(400, [], [], 'Can\'t edit distribusi because status project not a \'Survey\'', [], '');
                        $this->response($res, 400);
                    }
                }
            }
        }

        $data = array(
            'distribusi_kukd' => $this->put('distribusi_kukd'),
            'distribusi_dropcore' => ($this->put('distribusi_dropcore') == NULL) ? NULL : $this->put('distribusi_dropcore'),
            'distribusi_address' => $this->put('distribusi_address'),
            'distribusi_benchmark_address' => $this->put('distribusi_benchmark_address'),
            'distribusi_odp' => $this->put('distribusi_odp'),
            'distribusi_lg' => $this->put('distribusi_lg'),
            'distribusi_lt' => $this->put('distribusi_lt'),
            'distribusi_core' => $this->put('distribusi_core'),
            'distribusi_core_opsi' => $this->put('distribusi_core_opsi'),
            'distribusi_capacity' => $this->put('distribusi_capacity'),
            'distribusi_note' => $this->put('distribusi_note'),
        );

        $val = [
            'distribusi_kukd' => 'required|in:12,24,48',
            'distribusi_address' => 'required',
            'distribusi_benchmark_address' => 'required',
            'distribusi_odp' => 'required',
            'distribusi_lg' => 'required',
            'distribusi_lt' => 'required',
            'distribusi_core' => 'required',
            'distribusi_capacity' => 'required|in:8,16',
        ];

        if ($getDataProject['label_cat'] != '1') {
            $val['distribusi.*.distribusi_dropcore'] = 'required|integer';
        }

        $make = $this->validator->make($data, $val);

        $make->setAliases([
            'distribusi_kukd' => 'KU/KD',
            'distribusi_odp' => 'ODP',
            'distribusi_address' => 'Address',
            'distribusi_benchmark_address' => 'Benchmark Address',
            'distribusi_lg' => 'Longitude',
            'distribusi_lt' => 'Latitude',
            'distribusi_core' => 'Core',
            'distribusi_capacity' => 'Capacity',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $in = $this->GlobalModel->update('project_distribusi', $data, ['deleteAt' => NULL, 'distribusi_id' => $distribusi_id]);
            if ($in) {
                $res = formatResponse(200, [], [], '', [], 'Success update distribusi');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed update distribusi', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteDistribusi_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DDIS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $distribusi_id = $this->get('id');
        if ($distribusi_id == NULL) {
            $res = formatResponse(400, [], [], 'ID distribusi is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $distribusi_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data distribusi not found', [], '');
            $this->response($res, 404);
        } else {
            $getDataFeeder = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $getData['feeder_id']], false);
            if ($getDataFeeder == NULL) {
                $res = formatResponse(404, [], [], 'Data feeder not found', [], '');
                $this->response($res, 404);
            } else {
                $getDataProject = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $getDataFeeder['project_id']], false);
                if ($getDataProject == NULL) {
                    $res = formatResponse(404, [], [], 'Data project not found', [], '');
                    $this->response($res, 404);
                } else {
                    if ($getDataProject['project_status'] != 'Survey') {
                        $res = formatResponse(400, [], [], 'Can\'t delete distribusi because status project not a \'Survey\'', [], '');
                        $this->response($res, 400);
                    }
                }
            }
        }
        $in = $this->GlobalModel->delete('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $distribusi_id]);
        if ($in) {
            $res = formatResponse(200, [], [], '', [], 'Success delete distribusi');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete distribusi', [], '');
            $this->response($res, 400);
        }
    }

    public function addFileSurvey_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CFLS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'file' => $this->post('file'),
        );

        $make = $this->validator->make($data, [
            'file' => 'required',
        ]);

        $make->setAliases([
            'file' => 'File',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->checkFolder($project_id);
            $helper = new Base64fileUploads();
            $in = $helper->do_uploads('./assets/project/DATA-PROJECT-' . $project_id . '/survey/', $data['file']);
            if ($in['status']) {
                $data = [
                    'project_id' => $project_id,
                    'survey_file' => $in['data']['file_name'],
                    'direktori' => 'survey'
                ];
                $this->GlobalModel->insert('project_survey', $data);
                $res = formatResponse(200, [], [], '', [], $in['message']);
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], $in['message'], [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteFileSurvey_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DFLS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        $survey_id = $this->get('survey');
        if ($survey_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getDataSurvey = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id], false);
        if ($getDataSurvey == NULL) {
            $res = formatResponse(404, [], [], 'File survey not found', [], '');
            $this->response($res, 404);
        }
        $in = $this->GlobalModel->delete('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id]);
        if ($in) {
            $this->checkFolder($project_id);
            rename(DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/survey' . '/' . $getDataSurvey['survey_file'], DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/deleted/survey' . '/' . $getDataSurvey['survey_file']);
            $res = formatResponse(200, [], [], '', [], 'Success delete file');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete file', [], '');
            $this->response($res, 400);
        }
    }

    private function checkFolder($project_id)
    {
        $folder = 'DATA-PROJECT-' . $project_id;
        $folder = './assets/project/' . filename_safe($folder);
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        } else {
            if (!is_dir($folder . '/survey')) {
                mkdir($folder . '/survey', 0777, true);
            }
            if (!is_dir($folder . '/instalasi')) {
                mkdir($folder . '/instalasi', 0777, true);
            }
            if (!is_dir($folder . '/terminasi')) {
                mkdir($folder . '/terminasi', 0777, true);
            }
            if (!is_dir($folder . '/labeling')) {
                mkdir($folder . '/labeling', 0777, true);
            }
            if (!is_dir($folder . '/deleted')) {
                mkdir($folder . '/deleted', 0777, true);
            }

            if (is_dir($folder . '/deleted')) {
                if (!is_dir($folder . '/deleted' . '/survey')) {
                    mkdir($folder . '/deleted' . '/survey', 0777, true);
                }
                if (!is_dir($folder . '/deleted' . '/instansi')) {
                    mkdir($folder . '/deleted' . '/instansi', 0777, true);
                }
                if (!is_dir($folder . '/deleted' . '/terminasi')) {
                    mkdir($folder . '/deleted' . '/terminasi', 0777, true);
                }
                if (!is_dir($folder . '/deleted' . '/labeling')) {
                    mkdir($folder . '/deleted' . '/labeling', 0777, true);
                }
            }
        }
    }

    public function addKHSList_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CKHSL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t add khs because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        $getDataUser = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        if ($getDataUser == NULL) {
            $res = formatResponse(404, [], [], 'Data user not found', [], '');
            $this->response($res, 404);
        }
        $data = array(
            'project_id' => $project_id,
            'designator_id' => $this->post('designator_id'),
            'khs_list_qty' => $this->post('khs_list_qty'),
            'userCode' => $getDataUser['userCode']
        );

        $make = $this->validator->make($data, [
            'designator_id' => 'required',
            'khs_list_qty' => 'required|integer',
        ]);

        $make->setAliases([
            'designator_id' => 'Designator',
            'khs_list_qty' => 'Quantity',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->insert('project_khs_list', $data);
            if ($cek) {
                $data = $this->GlobalModel->getData('project_khs_list', ['khs_list_id' => $this->db->insert_id(), 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to create khs list');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to create khs list', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function updateKHSList_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UKHSL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t edit khs because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        $khs_list_id = $this->get('khs');
        if ($khs_list_id == NULL) {
            $res = formatResponse(400, [], [], 'ID list khs is required', [], '');
            $this->response($res, 400);
        }
        $getDataKHS = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'khs_list_id' => $khs_list_id], false);
        if ($getDataKHS == NULL) {
            $res = formatResponse(404, [], [], 'Data list khs not found', [], '');
            $this->response($res, 404);
        }
        $getDataUser = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        if ($getDataUser == NULL) {
            $res = formatResponse(404, [], [], 'Data user not found', [], '');
            $this->response($res, 404);
        }
        $data = array(
            'project_id' => $project_id,
            'designator_id' => $this->put('designator_id'),
            'khs_list_qty' => $this->put('khs_list_qty'),
            'userCode' => $getDataUser['userCode']
        );

        $make = $this->validator->make($data, [
            'designator_id' => 'required',
            'khs_list_qty' => 'required|integer',
        ]);

        $make->setAliases([
            'designator_id' => 'Designator',
            'khs_list_qty' => 'Quantity',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->update('project_khs_list', $data, ['khs_list_id' => $khs_list_id, 'deleteAt' => NULL]);
            if ($cek) {
                $data = $this->GlobalModel->getData('project_khs_list', ['khs_list_id' => $khs_list_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update khs list');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update khs list', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function updateKHSListSource_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['UKHSL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'KHS Check') {
                $res = formatResponse(400, [], [], 'Can\'t edit khs because status project not a \'KHS Check\'', [], '');
                $this->response($res, 400);
            }
        }
        $khs_list_id = $this->get('khs');
        if ($khs_list_id == NULL) {
            $res = formatResponse(400, [], [], 'ID list khs is required', [], '');
            $this->response($res, 400);
        }
        $getDataKHS = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'khs_list_id' => $khs_list_id], false);
        if ($getDataKHS == NULL) {
            $res = formatResponse(404, [], [], 'Data list khs not found', [], '');
            $this->response($res, 404);
        }
        $getDataUser = $this->UserModel->uniqueEmail($this->payload['data']['email']);
        if ($getDataUser == NULL) {
            $res = formatResponse(404, [], [], 'Data user not found', [], '');
            $this->response($res, 404);
        }
        $data = array(
            'khs_source' => $this->put('khs_source'),
            'stock_id' => $this->put('stock_id')
        );
        if ($data['khs_source'] == 'WITEL') {
            $vall = [
                'khs_source' => 'required|in:TA,WITEL',
                'stock_id' => 'required'
            ];
        } else {
            $vall = [
                'khs_source' => 'required|in:TA,WITEL'
            ];
        }

        $make = $this->validator->make($data, $vall);

        $make->setAliases([
            'khs_source' => 'Source',
            'stock_id' => 'Stock',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->update('project_khs_list', $data, ['khs_list_id' => $khs_list_id, 'deleteAt' => NULL]);
            if ($cek) {
                $data = $this->GlobalModel->getData('project_khs_list', ['khs_list_id' => $khs_list_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update khs list');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update khs list', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteKHSList_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DKHSL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Survey') {
                $res = formatResponse(400, [], [], 'Can\'t delete khs because status project not a \'Survey\'', [], '');
                $this->response($res, 400);
            }
        }
        $khs_list_id = $this->get('khs');
        if ($khs_list_id == NULL) {
            $res = formatResponse(400, [], [], 'ID list khs is required', [], '');
            $this->response($res, 400);
        }
        $getDataKHS = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'khs_list_id' => $khs_list_id], false);
        if ($getDataKHS == NULL) {
            $res = formatResponse(404, [], [], 'Data list khs not found', [], '');
            $this->response($res, 404);
        }

        $cek = $this->GlobalModel->delete('project_khs_list', ['khs_list_id' => $khs_list_id, 'deleteAt' => NULL]);
        if ($cek) {
            $res = formatResponse(200, [], [], '', [], 'Success to delete khs list');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed to delete khs list', [], '');
            $this->response($res, 400);
        }
    }

    public function toKHSCheck_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CTKHS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Survey') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id, 'direktori' => 'survey']);
                $check2 = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'project_id' => $project_id]);
                $check3 = $this->GlobalModel->getData('project_sitax', ['deleteAt' => NULL, 'project_id' => $project_id]);
                $check4 = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);
                if ($check == NULL || $check2 == NULL || $check3 == NULL || $check4 == NULL) {
                    if ($check4 != NULL) {
                        foreach ($check4 as $k => $v) {
                            $check5 = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'feeder_id' => $v['feeder_id']]);
                            if ($check5 == NULL) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'File survey, sitax, khs list, feeder, distribusi must be filled', [], '');
                                $this->response($res, 400);
                            }
                        }
                    }
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'File survey, sitax, khs list, feeder must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'KHS Check'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->db->trans_commit();
                    $res = formatResponse(200, [], [], '', [], 'Success change status');
                    $this->response($res, 200);
                }
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function khs_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CMKHS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'KHS Check') {
                $getDataUser = $this->UserModel->uniqueEmail($this->payload['data']['email']);
                if ($getDataUser == NULL) {
                    $res = formatResponse(404, [], [], 'Data user not found', [], '');
                    $this->response($res, 404);
                }

                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id, 'direktori' => 'survey']);
                $check2 = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'project_id' => $project_id]);
                $check3 = $this->GlobalModel->getData('project_sitax', ['deleteAt' => NULL, 'project_id' => $project_id]);
                $check4 = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);
                if ($check == NULL || $check2 == NULL || $check3 == NULL || $check4 == NULL) {
                    if ($check4 != NULL) {
                        foreach ($check4 as $k => $v) {
                            $check5 = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'feeder_id' => $v['feeder_id']]);
                            if ($check5 == NULL) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'File survey, sitax, khs list, feeder, distribusi must be filled', [], '');
                                $this->response($res, 400);
                            }
                        }
                    }
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'File survey, sitax, khs list, feeder must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    $getDataKHSList = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'project_id' => $project_id]);
                    foreach ($getDataKHSList as $k => $v) {
                        $witel_id = $getData['witel_id'];
                        $stock_id = $v['stock_id'];
                        $designator = $this->db
                            ->select('d.designator_id,d.product_id,d.designator_code,d.designator_desc,dp.material_price,dp.service_price,d.createAt,d.updateAt,d.deleteAt')
                            ->join('designator d', 'd.designator_id=dp.designator_id')
                            ->where(['dp.deleteAt' => NULL, 'dp.package_id' => $getDataUser['package_id'], 'dp.designator_id' => $v['designator_id']])
                            ->get('designator_package dp')
                            ->row_array();
                        if ($designator == NULL) {
                            $this->db->trans_rollback();
                            $res = formatResponse(400, [], [], 'Failed Approved Instalation, designator not found', [], '');
                            $this->response($res, 400);
                        }
                        $product_id = $designator['product_id'];
                        if ($v['khs_source'] == 'WITEL') {
                            $getStockWitel = $this->GlobalModel->getData('stock_witel', ['stock_id' => $stock_id, 'witel_id' => $witel_id, 'product_id' => $product_id, 'deleteAt' => NULL], false);
                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Failed Approved Instalation, product not found', [], '');
                                $this->response($res, 400);
                            }
                            if ($getStockWitel == NULL) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Failed Approved Instalation, product not found', [], '');
                                $this->response($res, 400);
                            }
                            $getProduct = $this->GlobalModel->getData('product', ['product_id' => $product_id], false);
                            if ($getProduct == NULL) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Failed Approved Instalation, product not found', [], '');
                                $this->response($res, 400);
                            }
                            $newStock = $getStockWitel['stock_qty'] - $v['khs_list_qty'];
                            if ($newStock < 0) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Failed Approved Instalation, stock product ' . $getProduct['product_name'] . ' is not enough', [], '');
                                $this->response($res, 400);
                            } else {
                                $this->GlobalModel->update('stock_witel', ['stock_qty' => $newStock], ['stock_id' => $getStockWitel['stock_id']]);
                                if ($this->db->trans_status() === FALSE) {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Failed to update stok', [], '');
                                    $this->response($res, 400);
                                }

                                $params = [
                                    'khs_list_material_price' => $designator['material_price'],
                                    'khs_list_service_price' => $designator['service_price'],
                                    'khs_list_material_total' => $designator['material_price'] * $v['khs_list_qty'],
                                    'khs_list_service_total' => $designator['service_price'] * $v['khs_list_qty']
                                ];
                                $up = $this->GlobalModel->update('project_khs_list', $params, ['khs_list_id' => $v['khs_list_id']]);
                                if ($this->db->trans_status() === FALSE) {
                                    $this->db->trans_rollback();
                                    $res = formatResponse(400, [], [], 'Failed to update khs list', [], '');
                                    $this->response($res, 400);
                                }
                            }
                        }
                    }
                    $material_price = 0;
                    $service_price = 0;
                    $getDataKHSList = $this->GlobalModel->getData('project_khs_list', ['deleteAt' => NULL, 'project_id' => $project_id]);
                    foreach ($getDataKHSList as $k =>  $v) {
                        $material_price += $v['khs_list_material_total'];
                        $service_price += $v['khs_list_service_total'];
                    }
                    $paramsKHS = [
                        'project_id' => $project_id,
                        'khs_material_total' => $material_price,
                        'khs_service_total' => $service_price
                    ];
                    $checkKHS = $this->GlobalModel->getData('project_khs', ['project_id' => $project_id, 'deleteAt' => NULL], false);
                    if ($checkKHS == NULL) {
                        $up = $this->GlobalModel->insert('project_khs', $paramsKHS);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $res = formatResponse(400, [], [], 'Failed add khs', [], '');
                            $this->response($res, 400);
                        }
                    } else {
                        $up = $this->GlobalModel->update('project_khs', $paramsKHS, ['khs_id' => $checkKHS['khs_id']]);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $res = formatResponse(400, [], [], 'Failed update khs', [], '');
                            $this->response($res, 400);
                        }
                    }
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Instalation'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed update status project', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(2, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed add khs', [], '');
                        $this->response($res, 400);
                    }
                    $this->db->trans_commit();
                    $res = formatResponse(200, [], [], '', [], 'Success update status project');
                    $this->response($res, 200);
                }
            } else {
                $res = formatResponse(400, [], [], 'Can\'t add khs', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function toApproveInstalation_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSAI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Instalation') {
                $data = array(
                    'userCode' => $this->post('userCode')
                );

                $make = $this->validator->make($data, [
                    'userCode' => 'required',
                ]);

                $make->setAliases([
                    'userCode' => 'User'
                ]);

                $make->validate();

                if ($make->fails()) {
                    $errors = $make->errors();
                    $err = $errors->firstOfAll();
                    $res = formatResponse(400, [], $err, '', [], '');
                    $this->response($res, 400);
                } else {
                    $this->db->trans_begin();
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Approved Instalation', 'userCode' => $this->post('userCode')], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    // $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Approved Instalation'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed Approved Instalation', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(3, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  Approved Instalation', [], '');
                        $this->response($res, 400);
                    }
                    $this->db->trans_commit();
                    $res = formatResponse(200, [], [], '', [], 'Success to Approved Instalation');
                    $this->response($res, 200);
                }
            } else {
                $res = formatResponse(400, [], [], 'Can\'t Approved Instalation', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function addFileInstalation_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CFLI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Approved Instalation') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Approved Instalation\'', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'file' => $this->post('file'),
        );

        $make = $this->validator->make($data, [
            'file' => 'required',
        ]);

        $make->setAliases([
            'file' => 'File',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->checkFolder($project_id);
            $helper = new Base64fileUploads();
            $in = $helper->do_uploads('./assets/project/DATA-PROJECT-' . $project_id . '/instalasi/', $data['file']);
            if ($in['status']) {
                $data = [
                    'project_id' => $project_id,
                    'survey_file' => $in['data']['file_name'],
                    'direktori' => 'instalasi'
                ];
                $this->GlobalModel->insert('project_survey', $data);
                $res = formatResponse(200, [], [], '', [], $in['message']);
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], $in['message'], [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteFileInstalation_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DFLI']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Approved Instalation') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Approved Instalation\'', [], '');
                $this->response($res, 400);
            }
        }
        $survey_id = $this->get('survey');
        if ($survey_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getDataSurvey = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id], false);
        if ($getDataSurvey == NULL) {
            $res = formatResponse(404, [], [], 'File instalation not found', [], '');
            $this->response($res, 404);
        }
        $in = $this->GlobalModel->delete('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id]);
        if ($in) {
            $this->checkFolder($project_id);
            rename(DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/instalasi' . '/' . $getDataSurvey['survey_file'], DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/deleted/instalasi' . '/' . $getDataSurvey['survey_file']);
            $res = formatResponse(200, [], [], '', [], 'Success delete file');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete file', [], '');
            $this->response($res, 400);
        }
    }

    public function toApproveTerminasi_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSAT']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Approved Instalation') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id, 'direktori' => 'instalasi'], false);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'File instalation must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Termination'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(4, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                        $this->response($res, 400);
                    }
                }
                $this->db->trans_commit();
                $res = formatResponse(200, [], [], '', [], 'Success change status');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function addFileTermination_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CFLT']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Termination') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Termination\'', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'file' => $this->post('file'),
        );

        $make = $this->validator->make($data, [
            'file' => 'required',
        ]);

        $make->setAliases([
            'file' => 'File',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->checkFolder($project_id);
            $helper = new Base64fileUploads();
            $in = $helper->do_uploads('./assets/project/DATA-PROJECT-' . $project_id . '/terminasi/', $data['file']);
            if ($in['status']) {
                $data = [
                    'project_id' => $project_id,
                    'survey_file' => $in['data']['file_name'],
                    'direktori' => 'terminasi'
                ];
                $this->GlobalModel->insert('project_survey', $data);
                $res = formatResponse(200, [], [], '', [], $in['message']);
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], $in['message'], [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteFileTermination_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DFLT']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Termination') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Termination\'', [], '');
                $this->response($res, 400);
            }
        }
        $survey_id = $this->get('survey');
        if ($survey_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getDataSurvey = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id], false);
        if ($getDataSurvey == NULL) {
            $res = formatResponse(404, [], [], 'File termination not found', [], '');
            $this->response($res, 404);
        }
        $in = $this->GlobalModel->delete('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id]);
        if ($in) {
            $this->checkFolder($project_id);
            rename(DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/terminasi' . '/' . $getDataSurvey['survey_file'], DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/deleted/terminasi' . '/' . $getDataSurvey['survey_file']);
            $res = formatResponse(200, [], [], '', [], 'Success delete file');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete file', [], '');
            $this->response($res, 400);
        }
    }

    public function toApproveValid3_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSV3']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Termination') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id, 'direktori' => 'terminasi'], false);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'File instalation must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Valid 3'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(5, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                        $this->response($res, 400);
                    }
                }
                $this->db->trans_commit();
                $res = formatResponse(200, [], [], '', [], 'Success change status');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    private $formatCompleteFeederAndDistribusi = [
        'feeder' => [
            [
                'feeder_id' => 1,
                'olt_gpon' => 0,
                'olt_slot' => 0,
                'otl_port' => 0,
                'output_feeder' => 0,
                'output_pasif' => 0,
            ],
            [
                'feeder_id' => 2,
                'olt_gpon' => 0,
                'olt_slot' => 0,
                'otl_port' => 0,
                'output_feeder' => 0,
                'output_pasif' => 0,
            ]
        ],
        'distribusi' => [
            [
                'distribusi_id' => 1,
                'hasil_ukur_odp_valid_3' => 4
            ],
            [
                'distribusi_id' => 1,
                'hasil_ukur_odp_valid_3' => 4
            ]
        ],
        'note' => "asdasd"
    ];

    public function completeFeederAndDistribusi_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CDSV3']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Valid 3') {
                $res = formatResponse(400, [], [], 'Can\'t add feeder because status project not a \'Valid 3\'', [], '');
                $this->response($res, 400);
            }
        }
        $valid = [];
        if ($this->post('feeder') == NULL || $this->post('feeder') == "[]") {
            $feeder = [];
        } else {
            $valid = [
                'feeder' => 'array',
                'feeder.*.feeder_id' => 'required',
                'feeder.*.olt_gpon' => 'required|integer',
                'feeder.*.olt_slot' => 'required|integer',
                'feeder.*.otl_port' => 'required|integer',
                'feeder.*.output_feeder' => 'required|numeric',
                'feeder.*.output_pasif' => 'required|numeric',
            ];
            $feeder = json_decode($this->post('feeder'), true);
        }

        if ($this->post('distribusi') == NULL || $this->post('distribusi') == "[]") {
            $distribusi = [];
        } else {
            $valid = [
                'distribusi' => 'array',
                'distribusi.*.distribusi_id' => 'required',
                'distribusi.*.hasil_ukur_odp_valid_3' => 'required|numeric',
            ];
            $distribusi = json_decode($this->post('distribusi'), true);
        }

        $data = array(
            'feeder' => $feeder,
            'distribusi' => $distribusi,
            'note' => $this->post('note'),
        );

        $make = $this->validator->make($data, $valid);

        $make->setAliases([
            'feeder.*.feeder_id' => 'Feeder ID',
            'feeder.*.olt_gpon' => 'GPON',
            'feeder.*.olt_slot' => 'Slot',
            'feeder.*.otl_port' => 'Port',
            'feeder.*.output_feeder' => 'Feeder',
            'feeder.*.output_pasif' => 'Pasif',
            'distribusi.*.distribusi_id' => 'Distribusi ID',
            'distribusi.*.hasil_ukur_odp_valid_3' => 'Hasil ukur ODP Valid 3',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->db->trans_begin();
            foreach ($data['feeder'] as $k => $v) {
                $check = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $v['feeder_id']], false);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Feeder not found', [], '');
                    $this->response($res, 400);
                } else {
                    $params = [
                        'olt_gpon' => $v['olt_gpon'],
                        'olt_slot' => $v['olt_slot'],
                        'otl_port' => $v['otl_port'],
                        'output_feeder' => $v['output_feeder'],
                        'output_pasif' => $v['output_pasif'],
                    ];
                    $this->GlobalModel->update('project_feeder', $params, ['feeder_id' => $v['feeder_id']]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed to update feeder', [], '');
                        $this->response($res, 400);
                    }
                }
            }
            foreach ($data['distribusi'] as $k => $v) {
                $check = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $v['distribusi_id']], false);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Distribusi not found', [], '');
                    $this->response($res, 400);
                } else {
                    $checkFeeder = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'feeder_id' => $check['feeder_id']], false);
                    if ($checkFeeder == NULL) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Feeder not found', [], '');
                        $this->response($res, 400);
                    } else {
                        $odp = str_replace('ODC', 'ODP', $checkFeeder['feeder_odc']) . '/00';
                    }
                    $params = [
                        'odp_valid_3' => $odp,
                        'hasil_ukur_odp_valid_3' => $v['hasil_ukur_odp_valid_3'],
                    ];
                    $this->GlobalModel->update('project_distribusi', $params, ['distribusi_id' => $v['distribusi_id']]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed to update distribusi', [], '');
                        $this->response($res, 400);
                    }
                }
            }
            $params = [
                'project_note' => $data['note']
            ];
            $this->GlobalModel->update('project', $params, ['project_id' => $getData['project_id']]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed to update project note', [], '');
                $this->response($res, 400);
            }
            $this->db->trans_commit();
            $res = formatResponse(200, [], [], '', [], 'Success to update feeder and distribusi');
            $this->response($res, 200);
        }
    }

    public function toApproveLabeling_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Valid 3') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Feeder must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    foreach ($check as $k => $v) {
                        if ($v['olt_gpon'] == NULL || $v['olt_slot'] == NULL || $v['otl_port'] == NULL || $v['output_feeder'] == NULL || $v['output_pasif'] == NULL) {
                            $this->db->trans_rollback();
                            $res = formatResponse(400, [], [], 'Feeder must be completed (GPON, SLOT, PORT, OUTPUT, OUTPUT PASIF)', [], '');
                            $this->response($res, 400);
                        }
                        $distri = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $v['distribusi_id']]);
                        foreach ($distri as $d => $s) {
                            if ($s['odp_valid_3'] == NULL || $s['hasil_ukur_odp_valid_3'] == NULL) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Distribusi must be completed (HASIL UKUR VALID 3)', [], '');
                                $this->response($res, 400);
                            }
                        }
                    }
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Labeling'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(6, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                        $this->response($res, 400);
                    }
                }
                $this->db->trans_commit();
                $res = formatResponse(200, [], [], '', [], 'Success change status');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function addFileLabeling_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CFLL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Labeling') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Labeling\'', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'file' => $this->post('file'),
        );

        $make = $this->validator->make($data, [
            'file' => 'required',
        ]);

        $make->setAliases([
            'file' => 'File',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->checkFolder($project_id);
            $helper = new Base64fileUploads();
            $in = $helper->do_uploads('./assets/project/DATA-PROJECT-' . $project_id . '/labeling/', $data['file']);
            if ($in['status']) {
                $data = [
                    'project_id' => $project_id,
                    'survey_file' => $in['data']['file_name'],
                    'direktori' => 'labeling'
                ];
                $this->GlobalModel->insert('project_survey', $data);
                $res = formatResponse(200, [], [], '', [], $in['message']);
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], $in['message'], [], '');
                $this->response($res, 400);
            }
        }
    }

    public function deleteFileLabeling_delete()
    {
        $permission = checkPermission($this->payload['data']['email'], ['DFLL']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Labeling') {
                $res = formatResponse(400, [], [], 'Can\'t add file because status project not a \'Labeling\'', [], '');
                $this->response($res, 400);
            }
        }
        $survey_id = $this->get('survey');
        if ($survey_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getDataSurvey = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id], false);
        if ($getDataSurvey == NULL) {
            $res = formatResponse(404, [], [], 'File instalation not found', [], '');
            $this->response($res, 404);
        }
        $in = $this->GlobalModel->delete('project_survey', ['deleteAt' => NULL, 'survey_id' => $survey_id]);
        if ($in) {
            $this->checkFolder($project_id);
            rename(DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/labeling' . '/' . $getDataSurvey['survey_file'], DIR . 'assets/project/DATA-PROJECT-' . $project_id . '/deleted/labeling' . '/' . $getDataSurvey['survey_file']);
            $res = formatResponse(200, [], [], '', [], 'Success delete file');
            $this->response($res, 200);
        } else {
            $res = formatResponse(400, [], [], 'Failed delete file', [], '');
            $this->response($res, 400);
        }
    }

    public function toApproveValid4_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSV4']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Labeling') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id, 'direktori' => 'labeling'], false);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'File labeling must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Valid 4'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(7, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                        $this->response($res, 400);
                    }
                }
                $this->db->trans_commit();
                $res = formatResponse(200, [], [], '', [], 'Success change status');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    private $formatCompleteDistribusi = [
        'distribusi' => [
            [
                'distribusi_id' => 1,
                'odp_valid_4' => 4,
                'hasil_ukur_odp_valid_4' => 4
            ],
            [
                'distribusi_id' => 1,
                'odp_valid_4' => 4,
                'hasil_ukur_odp_valid_4' => 4
            ]
        ],
        'note' => "asdasd"
    ];
    public function finalCompleteDistribusi_post()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CDV4']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] != 'Valid 4') {
                $res = formatResponse(400, [], [], 'Can\'t update distribusi because status project not a \'Valid 4\'', [], '');
                $this->response($res, 400);
            }
        }
        if (!json_decode($this->post('distribusi'), true)) {
            $res = formatResponse(400, [], [], 'Wrong format for update distribusi', [], '');
            $this->response($res, 400);
        }

        $data = array(
            'distribusi' => json_decode($this->post('distribusi'), true),
            'note' => $this->post('note'),
        );

        $make = $this->validator->make($data, [
            'distribusi' => 'array',
            'distribusi.*.distribusi_id' => 'required',
            'distribusi.*.hasil_ukur_odp_valid_4' => 'required|numeric',
        ]);

        $make->setAliases([
            'distribusi.*.distribusi_id' => 'Distribusi ID',
            'distribusi.*.odp_valid_4' => 'ODP Valid 4',
            'distribusi.*.hasil_ukur_odp_valid_4' => 'Hasil ukur ODP Valid 4',
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $this->db->trans_begin();
            foreach ($data['distribusi'] as $k => $v) {
                $check = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $v['distribusi_id']], false);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Distribusi not found', [], '');
                    $this->response($res, 400);
                } else {
                    $params = [
                        'odp_valid_4' => $v['odp_valid_4'],
                        'hasil_ukur_odp_valid_4' => $v['hasil_ukur_odp_valid_4'],
                    ];
                    $this->GlobalModel->update('project_distribusi', $params, ['distribusi_id' => $v['distribusi_id']]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed to update distribusi', [], '');
                        $this->response($res, 400);
                    }
                }
            }
            $params = [
                'project_note' => $data['note']
            ];
            $this->GlobalModel->update('project', $params, ['project_id' => $getData['project_id']]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $res = formatResponse(400, [], [], 'Failed to update project note', [], '');
                $this->response($res, 400);
            }
            $this->db->trans_commit();
            $res = formatResponse(200, [], [], '', [], 'Success to update feeder and distribusi');
            $this->response($res, 200);
        }
    }

    public function toApproveReconsiliasi_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSTD']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Valid 4') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project_feeder', ['deleteAt' => NULL, 'project_id' => $project_id]);
                if ($check == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Feeder must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    foreach ($check as $k => $v) {
                        if ($v['olt_gpon'] == NULL || $v['olt_slot'] == NULL || $v['otl_port'] == NULL || $v['output_feeder'] == NULL || $v['output_pasif'] == NULL) {
                            $this->db->trans_rollback();
                            $res = formatResponse(400, [], [], 'Feeder must be completed (GPON, SLOT, PORT, OUTPUT, OUTPUT PASIF)', [], '');
                            $this->response($res, 400);
                        }
                        $distri = $this->GlobalModel->getData('project_distribusi', ['deleteAt' => NULL, 'distribusi_id' => $v['distribusi_id']]);
                        foreach ($distri as $d => $s) {
                            if ($s['odp_valid_4'] == NULL || $s['hasil_ukur_odp_valid_4'] == NULL) {
                                $this->db->trans_rollback();
                                $res = formatResponse(400, [], [], 'Distribusi must be completed (HASIL UKUR VALID 4)', [], '');
                                $this->response($res, 400);
                            }
                        }
                    }
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Reconsiliasi'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(8, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->db->trans_commit();
                    $res = formatResponse(200, [], [], '', [], 'Success change status');
                    $this->response($res, 200);
                }
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function reconsiliasi_put()
    {
        $permission = checkPermission($this->payload['data']['email'], ['URECON']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if (
                $getData['project_status'] != 'Reconsiliasi'
            ) {
                $res = formatResponse(400, [], [], 'Can\'t edit project', [], '');
                $this->response($res, 400);
            }
        }
        $data = array(
            'project_reconsiliasi' => $this->put('project_reconsiliasi'),
        );

        $make = $this->validator->make($data, [
            'project_reconsiliasi' => 'required'
        ]);

        $make->setAliases([
            'project_reconsiliasi' => 'Reconsiliasi'
        ]);

        $make->validate();

        if ($make->fails()) {
            $errors = $make->errors();
            $err = $errors->firstOfAll();
            $res = formatResponse(400, [], $err, '', [], '');
            $this->response($res, 400);
        } else {
            $cek = $this->GlobalModel->update('project', $data, ['project_id' => $project_id]);
            if ($cek) {
                $data = $this->GlobalModel->getData('project', ['project_id' => $project_id, 'deleteAt' => NULL], false);
                $res = formatResponse(200, $data, [], '', [], 'Success to update project');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Failed to update project', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function toApprovePemberkasan_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CRTP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Reconsiliasi') {
                $this->db->trans_begin();
                $check = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
                if ($check['project_reconsiliasi'] == NULL) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Reconsiliasi must be filled', [], '');
                    $this->response($res, 400);
                } else {
                    $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Pemberkasan'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->recordJob(9, $project_id, date('Y-m-d'));
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                        $this->response($res, 400);
                    }
                    $this->db->trans_commit();
                    $res = formatResponse(200, [], [], '', [], 'Success change status');
                    $this->response($res, 200);
                }
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function toApproveSubmit_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CPTS']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Pemberkasan') {
                $this->db->trans_begin();
                $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Submit'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Failed change status', [], '');
                    $this->response($res, 400);
                }
                $this->recordJob(10, $project_id, date('Y-m-d'));
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                    $this->response($res, 400);
                }
                $this->db->trans_commit();
                $res = formatResponse(200, [], [], '', [], 'Success change status');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function toApprovePaid_get()
    {
        $permission = checkPermission($this->payload['data']['email'], ['CSTP']);
        if ($permission['status'] == false) {
            $this->response($permission['data'], 400);
        }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            if ($getData['project_status'] == 'Submit') {
                $this->db->trans_begin();
                $updateStatus = $this->GlobalModel->update('project', ['project_status' => 'Paid'], ['deleteAt' => NULL, 'project_id' => $project_id]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Failed change status', [], '');
                    $this->response($res, 400);
                }
                $this->recordJob(11, $project_id, date('Y-m-d'));
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $res = formatResponse(400, [], [], 'Failed  change status', [], '');
                    $this->response($res, 400);
                }
                $this->db->trans_commit();
                $res = formatResponse(200, [], [], '', [], 'Success change status');
                $this->response($res, 200);
            } else {
                $res = formatResponse(400, [], [], 'Can\'t change status', [], '');
                $this->response($res, 400);
            }
        }
    }

    public function getFile_get()
    {
        // $permission = checkPermission($this->payload['data']['email'], ['CSTD']);
        // if ($permission['status'] == false) {
        //     $this->response($permission['data'], 400);
        // }
        $project_id = $this->get('id');
        if ($project_id == NULL) {
            $res = formatResponse(400, [], [], 'ID project is required', [], '');
            $this->response($res, 400);
        }
        $direktori = $this->get('direktori');
        if ($direktori == NULL) {
            $res = formatResponse(400, [], [], 'Direktori is required', [], '');
            $this->response($res, 400);
        }
        $getData = $this->GlobalModel->getData('project', ['deleteAt' => NULL, 'project_id' => $project_id], false);
        if ($getData == NULL) {
            $res = formatResponse(404, [], [], 'Data project not found', [], '');
            $this->response($res, 404);
        } else {
            $return = [];
            $file = $this->GlobalModel->getData('project_survey', ['deleteAt' => NULL, 'project_id' => $project_id, 'direktori' => $direktori]);
            foreach ($file as $k => $v) {
                if ($v['direktori'] == 'survey') {
                    $return['survey'][] = [
                        'namaFile' => $v['survey_file'],
                        'downloadLink' => base_url('project/download/assets/') . 'DATA-PROJECT-' . $project_id . '--survey--' . $v['survey_file'],
                        'createAt' => $v['createAt']
                    ];
                }
                if ($v['direktori'] == 'instalasi') {
                    $return['instalasi'][] = [
                        'namaFile' => $v['survey_file'],
                        'downloadLink' => base_url('project/download/assets/') . 'DATA-PROJECT-' . $project_id . '--instalasi--' . $v['survey_file'],
                        'createAt' => $v['createAt']
                    ];
                }
                if ($v['direktori'] == 'terminasi') {
                    $return['terminasi'][] = [
                        'namaFile' => $v['survey_file'],
                        'downloadLink' => base_url('project/download/assets/') . 'DATA-PROJECT-' . $project_id . '--terminasi--' . $v['survey_file'],
                        'createAt' => $v['createAt']
                    ];
                }
                if ($v['direktori'] == 'labeling') {
                    $return['labeling'][] = [
                        'namaFile' => $v['survey_file'],
                        'downloadLink' => base_url('project/download/assets/') . 'DATA-PROJECT-' . $project_id . '--labeling--' . $v['survey_file'],
                        'createAt' => $v['createAt']
                    ];
                }
            }
            $res = formatResponse(200, $return, [], '', [], '');
            $this->response($res, 200);
        }
    }

    public function download_get()
    {
        $this->load->helper('download');
        $assets = $this->get('assets');
        if ($assets == NULL) {
            $res = formatResponse(400, [], [], 'Assets is required', [], '');
            $this->response($res, 400);
        }
        $dir = DIR . '/assets/project/' . str_replace("--", "/", $assets);
        force_download($dir, NULL);
    }

    public function backup_get()
    {
        $this->load->library('zip');
        $project = $this->get('id');
        if ($project == NULL) {
            $res = formatResponse(400, [], [], 'Code project is required', [], '');
            $this->response($res, 400);
        }
        $filename = "backup" . $project . ".zip";
        $path = DIR . '/assets/project/DATA-PROJECT-' . $project;

        // Add directory to zip
        $this->zip->read_dir($path, FALSE);

        // Save the zip file to archivefiles directory
        $this->zip->archive(DIR . '/assets/' . $filename);

        // Download
        $this->zip->download($filename);
    }

    private function recordJob($job_id, $project_id, $date_start)
    {
        if ($job_id == 1) {
            $this->GlobalModel->update('project_job', [
                'date_start' => $date_start
            ], [
                'job_id' => $job_id,
                'project_id' => $project_id
            ]);
        } else if ($job_id == 11) {
            $this->GlobalModel->update('project_job', [
                'date_start' => $date_start,
                'date_done' => $date_start
            ], [
                'job_id' => $job_id,
                'project_id' => $project_id
            ]);
        } else {
            $this->GlobalModel->update('project_job', [
                'date_start' => $date_start
            ], [
                'job_id' => $job_id,
                'project_id' => $project_id
            ]);
            $this->GlobalModel->update('project_job', [
                'date_done' => $date_start
            ], [
                'job_id' => $job_id - 1,
                'project_id' => $project_id
            ]);
        }
    }
}
