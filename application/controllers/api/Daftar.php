<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Daftar extends BD_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        $this->load->model('PemesanModel');
    }

    public function index_post()
    {
        $username       = $this->post('username');
        $password       = password_hash($this->post('password'), PASSWORD_DEFAULT);
        $nama           = $this->post('nama');
        $no_hp          = $this->post('no_hp');
        $email          = $this->post('email');
        $alamat         = $this->post('alamat');
        $jenis_kelamin  = $this->post('jenis_kelamin');

        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[pemesan.username]');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('no_hp', 'No Hp', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|is_unique[pemesan.email]');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|is_unique[pemesan.jenis_kelamin]');

        $this->form_validation->set_message('required', '%s harus terisi');
        $this->form_validation->set_message('is_unique', 'Karakter %s harus unik');

        if ($this->form_validation->run() == FALSE) {
            $response = [
                'status'    =>  'error',
                'data'      =>  '',
                'message'   =>  [
                    'username'      =>  strip_tags(form_error('username')),
                    'password'      =>  strip_tags(form_error('password')),
                    'nama'          =>  strip_tags(form_error('nama')),
                    'no_hp'         =>  strip_tags(form_error('no_hp')),
                    'email'         =>  strip_tags(form_error('email')),
                    'alamat'        =>  strip_tags(form_error('alamat')),
                    'jenis_kelamin' =>  strip_tags(form_error('jenis_kelamin')),
                ]
            ];
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        $data = [
            'username'      =>  $username,
            'password'      =>  $password,
            'nama'          =>  $nama,
            'no_hp'         =>  $no_hp,
            'email'         =>  $email,
            'alamat'        =>  $alamat,
            'jenis_kelamin' =>  $jenis_kelamin,
        ];

        $add = $this->PemesanModel->add($data);
        $data['id_pemesan'] = $add;
        $response = [
            'status'    =>  'success',
            'data'      =>  $data,
            'message'   =>  'Anda berhasil mendaftar'
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

}
