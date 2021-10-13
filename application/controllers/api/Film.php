<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Film extends BD_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();

        $this->load->model('FilmModel');
    }


    public function index_get()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $film = $this->FilmModel->get()->result_array();
        $response = [
            'status'    =>  'success',
            'data'      =>  $film,
            'message'   =>  ''
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $judul      = html_escape($this->post('judul'));
        $deskripsi  = html_escape($this->post('deskripsi'));
        $kategori   = html_escape($this->post('kategori'));

        $this->form_validation->set_rules('judul', 'Judul', 'required');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');
        $this->form_validation->set_message('required', '%s harus terisi');
        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'judul'     =>  strip_tags(form_error('judul')),
                    'deskripsi' =>  strip_tags(form_error('deskripsi')),
                    'kategori'  =>  strip_tags(form_error('kategori')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        $data = [
            'judul'         =>  $judul,
            'deskripsi'     =>  $deskripsi,
            'kategori'      =>  $kategori,
        ];

        $add = $this->FilmModel->add($data);
        $data['id'] = $add;
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menambahkan data film'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_put($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $film = $this->FilmModel->get_where(['id_film' => $id])->row_array();

        if (empty($film)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $judul      = $this->put('judul');
        $deskripsi  = $this->put('deskripsi');
        $kategori   = $this->put('kategori');

        $this->form_validation->set_data($this->put());
        $this->form_validation->set_rules('judul', 'Judul', 'required');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');
        $this->form_validation->set_message('required', '%s harus terisi');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'judul'     =>  strip_tags(form_error('judul')),
                    'deskripsi' =>  strip_tags(form_error('deskripsi')),
                    'kategori'  =>  strip_tags(form_error('kategori')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'judul'         =>  $judul,
            'deskripsi'     =>  $deskripsi,
            'kategori'      =>  $kategori,
        ];

        $this->FilmModel->edit(['id_film' => $id], $data);
        $data['id']     =   $id;

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil mengubah data film'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function index_delete($id)
    {
        $credential = $this->user_data;
        $this->adminAuthorization($credential);

        $film = $this->FilmModel->get_where(['id_film' => $id])->row_array();

        if (empty($film)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  'Data tidak ditemukan'
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }

        $data = [
            'id'            =>  $id,
            'judul'         =>  $film['judul'],
            'deskripsi'     =>  $film['deskripsi'],
            'kategori'      =>  $film['kategori'],
        ];

        $this->FilmModel->delete(['id_film' => $id]);
        $data['id']     =   $id;

        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil menghapus data film'
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
