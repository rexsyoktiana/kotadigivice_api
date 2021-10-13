<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tiket extends BD_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();

        $this->load->model('TiketModel');
        $this->load->model('JadwalModel');
        $this->load->model('PemesanModel');
    }

    public function index_get()
    {
        $credential = $this->user_data;
        // $this->adminAuthorization($credential);

        $ticket = null;
        if ($credential->role == 'admin') {
            $ticket = $this->TiketModel->get()->result_array();
        } elseif ($credential->role == 'pemesan') {
            $ticket = $this->TiketModel->get()->result_array();
        }
        $response = [
            'status'    =>  'success',
            'data'      =>  $ticket,
            'message'   =>  '',
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }


    public function index_post()
    {
        $credential = $this->user_data;
        // $this->adminAuthorization($credential);

        $id_jadwal  = $this->post('id_jadwal');
        $id_pemesan = $credential->id;


        $checkJadwal = $this->JadwalModel->get_where(['id_jadwal' => $id_jadwal])->row_array();
        if (empty($checkJadwal)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'ID Jadwal tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->form_validation->set_rules('id_jadwal', 'Jadwal', 'required');
        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'id_jadwal'    =>  strip_tags(form_error('id_jadwal')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        if ($checkJadwal['stok'] == 0) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Tiket sudah tidak tersedia'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'id_jadwal'     =>  $id_jadwal,
            'id_pemesan'    =>  $id_pemesan,
        ];

        $add = $this->TiketModel->add($data);

        $this->JadwalModel->edit(['id_jadwal' => $id_jadwal], ['stok' => $checkJadwal['stok'] - 1]);

        $data['id_tiket']  = $add;
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil membeli tiket'
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
