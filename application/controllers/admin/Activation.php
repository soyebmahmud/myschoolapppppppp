<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Activation extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('local_activation');
    }

    public function index()
    {
        if (!$this->local_activation->is_activated()) {
            redirect('admin/activation/activate');
            return;
        }

        $data['title'] = 'Application Activation';
        $data['status'] = $this->local_activation->get_status();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activation/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function activate()
    {
        if ($this->local_activation->is_activated()) {
            redirect('admin/activation');
            return;
        }

        $data['title'] = 'Application Activation';
        $data['error'] = $this->session->flashdata('activation_error');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/activation/activate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function process()
    {
        if ($this->local_activation->is_activated()) {
            redirect('admin/activation');
            return;
        }

        $this->form_validation->set_rules('activation_code', 'Activation Code', 'required|trim|xss_clean');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('activation_error', validation_errors());
            redirect('admin/activation/activate');
            return;
        }

        $activation_code = $this->input->post('activation_code', true);
        if ($this->local_activation->activate($activation_code)) {
            redirect('admin/activation');
            return;
        }

        $this->session->set_flashdata('activation_error', 'Invalid Activation Code');
        redirect('admin/activation/activate');
    }
}
