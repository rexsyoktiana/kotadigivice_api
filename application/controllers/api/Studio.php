<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Studio extends BD_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();

        $this->load->model('StudioModel');
    }

    public function index_get()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $studio = $this->StudioModel->get()->result_array();
        $response = [
            'status'    =>  'success',
            'data'      =>  $studio,
            'message'   =>  ''
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }


    public function index_post()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $nama = $this->post('nama');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'nama'     =>  strip_tags(form_error('nama')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        $data = [
            'nama_studio'         =>  $nama,
        ];

        $add = $this->StudioModel->add($data);
        $data['id_studio'] = $add;
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menambahkan data studio'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_put($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $studio = $this->StudioModel->get_where(['id_studio' => $id])->row_array();

        if (empty($studio)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $nama      = $this->put('nama');

        $this->form_validation->set_data($this->put());
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'nama'     =>  strip_tags(form_error('nama')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'nama_studio'         =>  $nama,
        ];

        $this->StudioModel->edit(['id_studio' => $id], $data);
        $data['id_studio']     =   $id;

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil mengubah data studio'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_delete($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $studio = $this->StudioModel->get_where(['id_studio' => $id])->row_array();

        if (empty($studio)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }


        $this->StudioModel->delete(['id_studio' => $id]);
        $data = [
            'id_studio'     =>  $id,
            'nama_studio'   =>  $studio['nama_studio'],
        ];

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menghapus data studio'
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
