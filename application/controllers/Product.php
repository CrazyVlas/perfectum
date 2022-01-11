<?php
defined('BASEPATH') or exit('No direc script acces allowed');

class Product extends CI_Controller {
    public function __construct(){
        parent::__construct();

        // Products db
        $this->load->model('product_model');

        // Categories db
        $this->load->model('category_model');

        // base_url()
        $this->load->helper('url');
    }

    public function index (){
        $data['title'] = "Продукты";
        $data['description'] = "Продукты";
        $data['products'] = $this->product_model->getProducts();
        $data['categories'] = $this->category_model->getCategories();

        $this->load->view('templates/header', $data);
        $this->load->view('perfectum/allProducts', $data);
        $this->load->view('templates/footer');
    }

    public function view ($slug = NULL){
        $data['product'] = $this->product_model->getProducts($slug);

        if(empty($data['product'])){
            show_404();
        }

        $data['title'] = $data['product']['title'];
        $data['description'] = $data['product']['description'];
        $data['name'] = $data['product']['name'];
        $data['content'] = $data['product']['content'];
        $data['price'] = $data['product']['price'];
        $data['stock'] = $data['product']['stock'];
        $data['created_at'] = $data['product']['created_at'];

        $this->load->view('templates/header', $data);
        $this->load->view('perfectum/oneProduct', $data);
        $this->load->view('templates/footer');
    }

    public function productDetails(){

        // POST data
        $postData = $this->input->post();

        // GET data
        $data = $this->product_model->getProductsSort($postData);

        echo $data;
    }

    public function productDelete(){

        // POST data
        $postData = $this->input->post();

        // Delete product
        $this->product_model->deleteProduct($postData);

    }

    public function productChangeStock(){

        // POST data
        $postData = $this->input->post();

        // Change product stock
        $this->product_model->updateProductStock($postData);

    }

    public function add(){

        // POST data
        $postData = $this->input->post();

        // Validate
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[100]');
        $this->form_validation->set_rules('price', 'Price', 'required|min_length[1]|integer');
        $this->form_validation->set_rules('stock', 'Stock', 'required|greater_than[-1]|less_than[2]|integer');

        $checkCategory = $this->category_model->getCategories($postData['category']);
        if(!$checkCategory){
            echo 'Категория не найдена';
            return false;
        }

        if ($this->form_validation->run() == FALSE) {
            echo validation_errors();
        } else {
            $this->product_model->addProduct($postData);
                echo $this->product_model->getProductsSort($postData);
        }
    }
}
