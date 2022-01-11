<?php
defined('BASEPATH') or exit('No direc script acces allowed');

class Category extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Categories db
        $this->load->model('category_model');
    }

    public function add(){
        $postData = $this->input->post();
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[100]');

        if ($this->form_validation->run() == FALSE) {
            echo validation_errors();
        } else {
           echo $this->category_model->addCategory($postData);
        }

    }
}