<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect('admin/admin/dashboard');
    }

    public function dashboard()
    {
        $this->load->view('layout/header');
        $this->load->view('admin/dashboard');
        $this->load->view('layout/footer');
    }

    public function activation()
    {
        $data['title'] = 'Application Activation';
        $data['status'] = $this->local_activation->get_status();
        if ($data['status']['active']) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/activation/index', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        $data['error'] = $this->session->flashdata('activation_error');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activation/activate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function process_activation()
    {
        $this->form_validation->set_rules('activation_code', 'Activation Code', 'required|trim|xss_clean');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('activation_error', validation_errors());
            redirect('admin/admin/activation');
            return;
        }

        $activation_code = $this->input->post('activation_code', true);
        if ($this->local_activation->activate($activation_code)) {
            redirect('admin/admin/activation');
            return;
        }

        $this->session->set_flashdata('activation_error', 'Invalid Activation Code');
        redirect('admin/admin/activation');
    }

    public function updateSession()
    {
        $session       = $this->input->post('popup_session');
        $session_array = $this->session->has_userdata('session_array');
        if ($session_array) {
            $this->session->unset_userdata('session_array');
        }
        $session       = $this->session_model->get($session);
        $session_array = array('session_id' => $session['id'], 'session' => $session['session']);
        $this->session->set_userdata('session_array', $session_array);

        echo json_encode(array('status' => 1, 'message' => $this->lang->line('session_changed_successfully')));
    }

    public function backup()
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/backup');
        $this->session->set_userdata('inner_menu', 'admin/backup');
        $data['title'] = $this->lang->line('backup_history');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            if ($this->input->post('backup') == "upload") {
                $file = $_FILES['db_file']['tmp_name'];
                if ($file) {
                    $this->load->library('backup');
                    $this->backup->import_database($file);
                }
            }
        }
        $data['list'] = $this->backup_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/backup/index', $data);
        $this->load->view('layout/footer', $data);
    }
}
