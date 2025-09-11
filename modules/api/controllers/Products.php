<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require __DIR__.'/REST_Controller.php';

class Products extends REST_Controller {

    const MODULE_UPLOAD_DIR = 'uploads/';
    const MODULE_URL = 'modules/products/uploads/';
    const MODULE_DIR = __DIR__.'/../../products/';
    
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        /*if (!file_exists(__DIR__.'/../../products/models/Products_model.php')) {
       		echo 'no';
       	}
       	require_once __DIR__.'/../../products/models/Products_model.php';*/

        $this->load->model('Api_products_model');

    }

    public function data_get($data = '',$id='')
    {
        // If the id parameter doesn't exist return all the
        $data = $this->Api_products_model->get($id);
        /*echo $this->db->last_query();
        die;*/
        // Check if the data store contains
        if ($data){
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = $value;
                $response[$key]['product_image'] = empty($value['product_image']) ? null : base_url(self::MODULE_URL.$value['product_image']);
            }
            $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }else{
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No data were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function data_search_get($data_search = '',$key='')
    {
        // If the id parameter doesn't exist return all the
        $data = $this->Api_products_model->search($key);

        // Check if the data store contains
        if ($data){
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }else{
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No data were found'

            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code

        }

    }

    public function data_delete($data = '',$id=''){ 
        $id = $this->security->xss_clean($id);
        if(empty($id) && !is_numeric($id)){
            $message = array(
                'status' => FALSE,
                'message' => 'Invalid Product ID'
            );
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            exit();
        }

        $product_images = $this->Api_products_model->get_product_images($id);

            
        $output = $this->Api_products_model->delete($id);
        if($output === TRUE){
            
            foreach ($product_images as $key => $product_image) {
                unlink(self::MODULE_DIR.self::MODULE_UPLOAD_DIR.$product_image['image']);
            }
            // success
            $message = array(
                'status' => TRUE,
                'message' => 'Product Delete Successful.'
            );
            $this->response($message, REST_Controller::HTTP_OK);
        }else{
            // error
            $message = array(
                'status' => FALSE,
                'message' => 'Product Delete Fail.'
            );
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        }


    }
}