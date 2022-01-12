<?php

class Product_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }

    public function getProducts ($slug = FALSE){
        if ($slug === FALSE){
            $this->db->select('products.id, categories.name as category, products.name, products.stock, products.price, products.created_at');
            $this->db->join('categories', 'categories.id = products.category_id');
            $query = $this->db->get('products');
            return $query->result_array();
        }
        $query = $this->db->get_where('products', array('slug' => $slug));
        return $query->row_array();
    }

    public function getProductsSort ($postData = array()){
        $response = array();

        if(isset($postData['categoryFilter']) and isset($postData['stockFilter'])){
            $this->db->select('products.id, categories.name as category, products.name, products.stock, products.price, products.created_at');
            $this->db->join('categories', 'categories.id = products.category_id');

            if ($postData['categoryFilter']) {
                $this->db->where('category_id', $postData['categoryFilter']);
            }

            if ($postData['stockFilter']) {
                $this->db->where('stock', $postData['stockFilter']-1);
            }

            $records = $this->db->get('products');
            $response = $records->result_array();

            $output = '';

            foreach ($response as $product){
                $stock = ($product['stock'] ? 'bg-success' : 'bg-danger');

                $output .= '   
                <div class="row mt-2 p-3 text-light product '. $stock .' " id="product" data-id=" '. $product['id'] .' ">
                    <div class="name col-4">
                        '. $product['name'] .'
                    </div>
        
                    <div class="name col-2">
                        '. $product['category'] .'
                    </div>
        
                    <div class="price col-1">
                        '. $product['price'] .'
                    </div>
        
                    <div class="created_at col-2">
                        '. $product['created_at'] .'
                    </div>
        
                    <div class="stock col-2">
                        <select class="form-select" aria-label="Default select example" id="productStock">
                                <option disabled selected>Наличие</option>
                                <option value="1">В наличии</option>
                                <option value="0">Продано</option>
                        </select>
                    </div>
        
                    <div class="deleted col-1 text-center">
                        <i class="fas fa-trash-alt" id="deleted"></i>
                    </div>
            </div>
        ';

            }
            return $output;
        }
    }

    public function deleteProduct ($postData = array()){
        $this->db->where('id', $postData['product']);
        $this->db->delete('products');
    }

    public function updateProductStock ($postData = array()){
        $this->db->set('stock', $postData['stock']);
        $this->db->where('id', $postData['product']);
        $this->db->update('products');
    }

    public function addProduct ($postData = array()){
        $this->load->helper('date');

        $data = array(
            'name' => $postData['name'],
            'price' => $postData['price'],
            'category_id' => $postData['category'],
            'stock' => $postData['stock'],
            'created_at' => mdate("%Y-%m-%d %H:%i:%s"),
    );
        $this->db->insert('products', $data);

    }
}