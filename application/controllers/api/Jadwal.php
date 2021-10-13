<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal extends BD_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();

        $this->load->model('JadwalModel');
        $this->load->model('FilmModel');
        $this->load->model('StudioModel');
    }

    public function index_get()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $jadwal = $this->JadwalModel->get()->result_array();
        $response = [
            'status'    =>  'success',
            'data'      =>  $jadwal,
            'message'   =>  '',
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }


    public function index_post()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $jadwal     = date('Y-m-d H:i:s', strtotime($this->post('jadwal')));
        $stok       = $this->post('stok');
        $id_film    = $this->post('id_film');
        $id_studio  = $this->post('id_studio');

        $checkFilm  = $this->FilmModel->get_where(['id_film' => $id_film])->row_array();
        if (empty($checkFilm)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Film tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        $checkStudio = $this->StudioModel->get_where(['id_studio' => $id_studio])->row_array();
        if (empty($checkStudio)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Studio tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $where = ['jadwal' => $jadwal, 'id_film' => $id_film, 'id_studio' => $id_studio];
        $checkDuplicate = $this->JadwalModel->get_where($where)->row_array();
        if (!empty($checkDuplicate)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak boleh duplikat'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->form_validation->set_rules('jadwal', 'Jadwal', 'required');
        $this->form_validation->set_rules('stok', 'Stok', 'required');
        $this->form_validation->set_rules('id_film', 'Id Film', 'required');
        $this->form_validation->set_rules('id_studio', 'Id Studio', 'required');

        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'jadwal'    =>  strip_tags(form_error('jadwal')),
                    'stok'      =>  strip_tags(form_error('stok')),
                    'id_film'   =>  strip_tags(form_error('id_film')),
                    'id_studio' =>  strip_tags(form_error('id_studio')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'jadwal'    =>  $jadwal,
            'stok'      =>  $stok,
            'id_film'   =>  $id_film,
            'id_studio' =>  $id_studio,
        ];

        $add = $this->JadwalModel->add($data);

        $data['id_jadwal']  = $add;
        $data['jadwal']     = date('d-m-Y H:i:s', strtotime($jadwal));
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menambahkan data jadwal'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_put($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $jadwal     = date('Y-m-d H:i:s', strtotime($this->put('jadwal')));
        $stok       = $this->put('stok');
        $id_film    = $this->put('id_film');
        $id_studio  = $this->put('id_studio');

        $getJadwal  = $this->JadwalModel->get_where(['id_jadwal' => $id])->row_array();
        if (empty($getJadwal)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Jadwal tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $checkFilm  = $this->FilmModel->get_where(['id_film' => $id_film])->row_array();
        if (empty($checkFilm)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Film tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        $checkStudio = $this->StudioModel->get_where(['id_studio' => $id_studio])->row_array();
        if (empty($checkStudio)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Studio tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        if ($jadwal != $getJadwal['jadwal'] || $id_film != $getJadwal['id_film'] || $id_studio != $getJadwal['id_studio']) {
            $where = [
                'jadwal'    =>  $jadwal,
                'id_film'   =>  $id_film,
                'id_studio' =>  $id_studio
            ];

            $checkDuplicate = $this->Jadwal->get_where($where)->row_array();
            if (empty($checkKursi)) {
                $response = [
                    'status'    =>  'error',
                    'data'      =>  '',
                    'message'   =>  'Data tidak boleh duplikat'
                ];
                $this->response($response, REST_Controller::HTTP_NOT_FOUND);
            }
        }

        $this->form_validation->set_data($this->put());
        $this->form_validation->set_rules('jadwal', 'Jadwal', 'required');
        $this->form_validation->set_rules('stok', 'Stok', 'required|integer');
        $this->form_validation->set_rules('id_film', 'Id Film', 'required');
        $this->form_validation->set_rules('id_studio', 'Id Studio', 'required');

        $this->form_validation->set_message('required', '%s harus terisi');
        $this->form_validation->set_message('integer', 'Karakter %s harus angka');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'jadwal'    =>  strip_tags(form_error('jadwal')),
                    'stok'      =>  strip_tags(form_error('stok')),
                    'id_film'   =>  strip_tags(form_error('id_film')),
                    'id_studio' =>  strip_tags(form_error('id_studio')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'jadwal'    =>  $jadwal,
            'stok'      =>  $stok,
            'id_film'   =>  $id_film,
            'id_studio' =>  $id_studio,
        ];

        $this->JadwalModel->edit(['id_jadwal' => $id], $data);
        $data['id_jadwal']     =   $id;
        $data['jadwal']     = date('d-m-Y H:i:s', strtotime($jadwal));
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil mengubah data jadwal'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_delete($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $jadwal = $this->JadwalModel->get_where(['id_jadwal' => $id])->row_array();

        if (empty($jadwal)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }


        $this->JadwalModel->delete(['id_jadwal' => $id]);
        $data = [
            'id_jadwal' =>  $id,
            'jadwal'    =>  date('d-m-Y H:i:s', strtotime($jadwal['jadwal'])),
            'stok'      =>  $jadwal['stok'],
            'id_film'   =>  $jadwal['id_film'],
            'id_studio' =>  $jadwal['id_studio'],
        ];

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menghapus data jadwal'
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
