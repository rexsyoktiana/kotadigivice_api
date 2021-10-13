<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kursi extends BD_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();

        $this->load->model('KursiModel');
        $this->load->model('StudioModel');
    }

    public function index_get()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $studio = $this->KursiModel->get()->result_array();
        $response = [
            'status'    =>  'success',
            'data'      =>  $studio,
            'message'   =>  '',
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }


    public function index_post()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $no_kursi       = $this->post('no_kursi');
        $id_studio  = $this->post('id_studio');

        $checkStudio = $this->StudioModel->get_where(['id_studio' => $id_studio])->row_array();
        if (empty($checkStudio)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Studio tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $where = ['no_kursi' => $no_kursi, 'id_studio' => $id_studio];
        $checkDuplicate = $this->KursiModel->get_where($where)->row_array();
        if (!empty($checkDuplicate)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak boleh duplikat'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->form_validation->set_rules('no_kursi', 'Nomer Kursi', 'required');
        $this->form_validation->set_rules('id_studio', 'Id Studio', 'required');

        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'no_kursi'     =>  strip_tags(form_error('no_kursi')),
                    'id_studio'     =>  strip_tags(form_error('id_studio')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'no_kursi'  =>  $no_kursi,
            'id_studio' =>  $id_studio,
        ];

        $add = $this->KursiModel->add($data);

        $data['id_kursi'] = $add;
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menambahkan data kursi'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_put($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $id_studio = $this->put('id_studio');
        $studio = $this->StudioModel->get_where(['id_studio' => $id_studio])->row_array();
        if (empty($studio)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Studio tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $checkKursi = $this->KursiModel->get_where(['id_kursi' => $id])->row_array();
        if (empty($checkKursi)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $no_kursi      = $this->put('no_kursi');

        $this->form_validation->set_data($this->put());
        $this->form_validation->set_rules('no_kursi', 'No kursi', 'required');
        $this->form_validation->set_rules('id_studio', 'Id Studio', 'required');
        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'no_kursi'     =>  strip_tags(form_error('no_kursi')),
                    'id_studio'     =>  strip_tags(form_error('id_studio')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'no_kursi'         =>  $no_kursi,
            'id_studio'         =>  $id_studio,
        ];

        $this->KursiModel->edit(['id_kursi' => $id], $data);
        $data['id_kursi']     =   $id;

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil mengubah data kursi'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_delete($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $kursi = $this->KursiModel->get_where(['id_kursi' => $id])->row_array();

        if (empty($kursi)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }


        $this->KursiModel->delete(['id_kursi' => $id]);
        $data = [
            'id_kursi'      =>  $id,
            'no_kursi'      =>  $kursi['no_kursi'],
            'id_studio'     =>  $kursi['id_studio'],
        ];

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menghapus data kursi'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function adminAuthorization($credential)
    {
        if ($credential->role != 'admin') {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'no authorized'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
