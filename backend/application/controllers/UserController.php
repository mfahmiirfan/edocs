<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends CI_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('User', 'user');
        $this->load->model('Role', 'role');

        $this->load->helper('cookie');
    }

    public function index()
    {
        $params = $this->input->get();

        $data = $this->user->findAll($params);
        if (!$data) {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function getPaginated()
    {
        $filter = $this->input->get();

        $data = $this->user->findAllPaginated($filter);
        if (!$data) {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function show($encrypted)
    {
        $id = decryptID($encrypted);
        $data = $this->user->find($id);
        if (!$data) {
            $data = (object)[];
        }
        $data['sli_edocs_users_id'] = encryptID($data['sli_edocs_users_id']);
        $data['submenus'] = $this->user->getSubmenusByUserId($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function showData($id)
    {
        $data = $this->user->find($id);
        if (!$data) {
            $data = (object)[];
        }
        $data['sli_edocs_users_id'] = encryptID($data['sli_edocs_users_id']);
        $data['submenus'] = $this->user->getSubmenusByUserId($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function store()
    {
        $data = $this->input->post();

        if ($this->user->save($data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'User stored successfully'
                ]));
        }
    }

    public function update($encrypted)
    {
        $id = decryptID($encrypted);
		//$id = $encrypted;
        $data = $this->input->post();

        if ($this->user->update($id, $data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'User updated successfully'
                ]));
        }
    }

    public function delete($encrypted)
    {
        $id = decryptID($encrypted);
        $data = $this->input->get();
        if ($this->user->destroy($id, $data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'User deleted successfully'
                ]));
        }
    }

    public function login()
    {
        $data = $this->input->post();
        if (!$this->user->isValid($data)) {
            delete_cookie('edocs_auth');

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode([
                    'message' => 'Invalid username or password.'
                ]));
            return;
        }

        $user = $this->user->findAll(['sli_edocs_users_nik' => $data['username']/*,'company_code'=>$data['company_code']*/]);

        $date = new DateTime();
        $payload['id'] = $user['sli_edocs_users_id'];
        $payload['username'] = $user['sli_edocs_users_nik'];
        $payload['name'] = $user['sli_edocs_users_name'];
        $payload['role_id'] = $user['sli_edocs_users_role_id'];
        $payload['role_name'] = $user['role_name'];
        $payload['company_code'] = $user['company_code'];
        $payload['company_id'] = $user['sli_edocs_company_id'];
        $payload['department_id'] = $user['sli_edocs_department_id'];
        $payload['iat'] = $date->getTimestamp();
        $payload['exp'] = $date->getTimestamp() + 60 * 60 * 2;

        $token = JWT::encode($payload, $this->config->item('jwt_key'), 'HS256');
        $cookie = array(
            'name'   => 'edocs_auth',
            'value'  => $token,
            'expire' => 60 * 60 * 2,
            'httponly' => true,
        );
        $this->input->set_cookie($cookie);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'message' => 'Login successfully',
                'role' => $this->role->find($user['sli_edocs_users_role_id'])
            ]));
    }

    public function logout()
    {
        delete_cookie('edocs_auth');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'message' => 'User logout successfully'
            ]));
    }

}
