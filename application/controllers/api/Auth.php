<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Auth extends BD_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

        $this->load->model('AdminModel');
        $this->load->model('PemesanModel');

    }



    public function login_post()
    {

        $key            = $this->config->item('thekey');
        $invalidLogin   = ['status' =>  'Invalid Login'];

        $username       = $this->input->post('username');
        $password       = $this->input->post('password');

        $admin          = $this->AdminModel->get_where(['username' => $username])->row_array();
        $id             = null;
        $role           = null;

        if (!empty($admin)) {
            if (password_verify($password, $admin['password'])) {
                $id             = $admin['id_admin'];
                $role           = 'admin';
                $token['id']    = $id;
                $token['role']  = $role;
            } else {
                $this->response($invalidLogin, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $pemesan    = $this->PemesanModel->get_where(['username' => $username])->row_array();
            if (!empty($pemesan)) {
                if (password_verify($password, $pemesan['password'])) {
                    $id             = $pemesan['id_pemesan'];
                    $role           = 'pemesan';
                    $token['id']    = $id;
                    $token['role']  = $role;
                } else {
                    $this->response($invalidLogin, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $this->response($invalidLogin, REST_Controller::HTTP_NOT_FOUND);
            }
        }

        $token['username'] = $username;
        $date = new DateTime();
        $token['iat'] = $date->getTimestamp();
        $token['exp'] = $date->getTimestamp() + 60 * 60 * 5;

        $output['status'] = 'success';
        $output['data'] = [
            'id'        =>  $id,
            'username'  =>  'username',
            'role'      =>  $role,
        ];
        $output['token'] = JWT::encode($token, $key);
        $this->set_response($output, REST_Controller::HTTP_OK);
    }
}
