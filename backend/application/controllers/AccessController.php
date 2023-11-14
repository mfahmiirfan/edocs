<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AccessController extends CI_Controller {
    public $is_token_verify_hookable=TRUE;
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Access','access');
        $this->load->model('User','user');

        $this->load->helper('cookie');
    }

    public function check()
    {
        $token = get_cookie('edocs_auth');
        
        $decoded = JWT::decode(/*explode(' ',$token)[1]*/$token, new Key($this->config->item('jwt_key'), 'HS256'));
        
        $data = json_decode(json_encode($decoded), true);

        $user = $this->user->find($data['id']);
        $data['department_id']=$user['sli_edocs_department_id'];
        $data['role_id']=$user['sli_edocs_users_role_id'];
        $data['role_name']=$user['sli_edocs_users_role_name'];
        $data['access']=$this->access->findAccessByUserId($data['id']);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}