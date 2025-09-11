<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require __DIR__.'/REST_Controller.php';
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 */
class Invoices extends REST_Controller {

	const MODULE_UPLOAD_DIR = 'uploads/';
	const MODULE_URL = 'modules/products/uploads/';
	const MODULE_DIR = __DIR__.'/../../products/';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Api_model');
    }

    /**
     * @api {get} api/leads/:id Request lead information
     * @apiName GetLead
     * @apiGroup Lead
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {Number} id Lead unique ID.
     *
     * @apiSuccess {Object} Lead information.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "id": "17",
     *         "hash": "c6e938f8b7a40b1bcfd98dc04f6eeee0-60d9c039da373a685fc0f74d4bfae631",
     *         "name": "Lead name",
     *         "contact": "",
     *         "title": "",
     *         "company": "Themesic Interactive",
     *         "description": "",
     *         "country": "243",
     *         "zip": null,
     *         "city": "London",
     *         "state": "London",
     *         "address": "1a The Alexander Suite Silk Point",
     *         "assigned": "5",
     *         "dateadded": "2019-07-18 08:59:28",
     *         "from_form_id": "0",
     *         "status": "0",
     *         "source": "4",
     *         ...
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_get($id = '')
    {
        // If the id parameter doesn't exist return all the
        $data = $this->Api_model->get_table('invoices', $id);

        // Check if the data store contains
        if ($data)
        {
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No data were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
     * @api {get} api/leads/search/:keysearch Search Lead Information.
     * @apiName GetLeadSearch
     * @apiGroup Lead
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} keysearch Search Keywords.
     *
     * @apiSuccess {Object} Lead information.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "id": "17",
     *         "hash": "c6e938f8b7a40b1bcfd98dc04f6eeee0-60d9c039da373a685fc0f74d4bfae631",
     *         "name": "Lead name",
     *         "contact": "",
     *         "title": "",
     *         "company": "Themesic Interactive",
     *         "description": "",
     *         "country": "243",
     *         "zip": null,
     *         "city": "London",
     *         "state": "London",
     *         "address": "1a The Alexander Suite Silk Point",
     *         "assigned": "5",
     *         "dateadded": "2019-07-18 08:59:28",
     *         "from_form_id": "0",
     *         "status": "0",
     *         "source": "4",
     *         ...
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_search_get($key = '')
    {
        $data = $this->Api_model->search('lead', $key);
        // Check if the data store contains
        if ($data)
        {
            // Set the response and exit
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No data were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
    /**
     * @api {post} api/leads Add New Lead
     * @apiName PostLead
     * @apiGroup Lead
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} source            Mandatory Lead source.
     * @apiParam {String} status            Mandatory Lead Status.
     * @apiParam {String} name              Mandatory Lead Name.
     * @apiParam {String} [assigned]        Optional Lead assigned.
     * @apiParam {String} [client_id]       Optional Lead From Customer.
     * @apiParam {String} [tags]            Optional Lead tags.
     * @apiParam {String} [contact]         Optional Lead contact.
     * @apiParam {String} [title]           Optional Position.
     * @apiParam {String} [email]           Optional Lead Email Address.
     * @apiParam {String} [website]         Optional Lead Website.
     * @apiParam {String} [phonenumber]     Optional Lead Phone.
     * @apiParam {String} [company]         Optional Lead company.
     * @apiParam {String} [address]         Optional Lead address.
     * @apiParam {String} [city]            Optional Lead City.
     * @apiParam {String} [state]           Optional Lead state.
     * @apiParam {String} [country]         Optional Lead Country.
     * @apiParam {String} [default_language]        Optional Lead Default Language.
     * @apiParam {String} [description]             Optional Lead description.
     * @apiParam {String} [custom_contact_date]     Optional Lead From Customer.
     * @apiParam {String} [contacted_today]         Optional Lead Contacted Today.
     * @apiParam {String} [is_public]               Optional Lead google sheet id.
     *
     * @apiParamExample {Multipart Form} Request-Example:
     *  array (size=20)
     *     'status' => string '2' (length=1)
     *     'source' => string '6' (length=1)
     *     'assigned' => string '1' (length=1)
     *     'client_id' => string '5' (length=1)
     *     'tags' => string '' (length=0)
     *     'name' => string 'Lead Name' (length=9)
     *     'contact' => string 'Contact A' (length=9)
     *     'title' => string 'Position A' (length=10)
     *     'email' => string 'AAA@gmail.com' (length=13)
     *     'website' => string '' (length=0)
     *     'phonenumber' => string '123456789' (length=9)
     *     'company' => string 'Themesic Interactive' (length=20)
     *     'address' => string '710-712 Cách Mạng Tháng Tám, P. 5, Q. Tân Bình' (length=33)
     *     'city' => string 'London' (length=6)
     *     'state' => string '' (length=0)
     *     'default_language' => string 'english' (length=10)
     *     'description' => string 'Description' (length=11)
     *     'custom_contact_date' => string '' (length=0)
     *     'is_public' => string 'on' (length=2)
     *     'contacted_today' => string 'on' (length=2)
     *
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Lead add successful.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Lead add successful."
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message add fail.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Lead add fail."
     *     }
     * 
     */
    public function data_post()
    {
        // form validation
        $this->form_validation->set_rules('clientid', 'Client Id required', 'trim|required|max_length[600]', array('is_unique' => 'This %s already exists please enter another Lead Name'));
        $this->form_validation->set_rules('number', 'Number: next invoice number is required', 'trim|required', array('is_unique' => 'This %s already exists please enter another Lead source'));
        $this->form_validation->set_rules('date', 'Date: Date is required', 'trim|required', array('is_unique' => 'This %s already exists please enter another Status'));
  
	  if ($this->form_validation->run() == FALSE)
        {
            // form validation error
            $message = array(
                'status' => FALSE,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors() 
            );
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        }
        else
        {
            $product_id=false;
            $this->load->model('products/products_model');
			 $this->load->model('payment_modes_model');
			$this->load->model('currencies_model');
			$this->load->model('invoices_model');
			$this->load->model('products/order_model');
			
			
            $order_data = $post = $this->input->post();
			$clientname="";
			$clientemail="";
			$clientphone="";
			
			if(isset($order_data['name'])){
				$clientname=$order_data['name'];
				unset($order_data['name']);
				unset($post['name']);
			}
			if(isset($order_data['phonenumber'])){
				$clientphone=$order_data['phonenumber'];
				unset($order_data['phonenumber']);
				unset($post['phonenumber']);
			}
			if(isset($order_data['email'])){
				$clientemail=$order_data['email'];
				
				$clientEmails=$this->clients_model->get_contact_by_email($clientemail);
				//if user exists then get client id and set the client id 
				if($clientEmails){
					
					unset($post['clientid']);
					$post['clientid']=$clientEmails->userid;
					
					
				}else{
					//create customer and contact and get client id.
				
					$clientInfo=array(
					'company'=>$clientname,
					'phonenumber'=>$clientphone,
					'address'=>$clientname." ".$order_data['billing_street'],
					'city'=>$order_data['billing_city'],
					'state'=>$order_data['billing_state'],
					'zip'=>$order_data['shipping_zip'],
					'country'=>235
                     );
					 $client_id=$this->clients_model->add($clientInfo);
					 
					if($client_id){
						
						$post['clientid']= $client_id;
						$customerInfo=array(
							
							'firstname'=>$clientname,
							'lastname'=>"",
							'email'=>$clientemail,
							'phonenumber'=>$clientphone,
							'is_primary'=>1
							
							
                         );
						//$customerID=$this->contracts_model->add($customerInfo);
						$this->clients_model->add_contact($customerInfo, $client_id, $not_manual_request = false);
					}
					
				}
				
				unset($order_data['email']);
				unset($post['email']);
			}
		    //lets update billing and shipping 
			 $transactions_update = [
                'billing_street'   =>$clientname." ". $post['billing_street'],
                'billing_city'     => $post['billing_city'],
                'billing_state'    => $post['billing_state'],
                'billing_zip'      => $post['billing_zip'],
                'billing_country'  => 235,
                'shipping_street'  => $post['shipping_street'],
                'shipping_city'    => $post['shipping_city'],
                'shipping_state'   => $post['shipping_state'],
                'shipping_zip'     => $post['shipping_zip'],
                'shipping_country' => 235,
                'country' => 235,
				'city' => $post['billing_city'],
				'zip' => $post['billing_zip'],
				'state' => $post['billing_state'],
				'address' => $post['billing_street'],
            ];
			$this->clients_model->update($transactions_update,$post['clientid']);
			

			 if (!empty($post)) {
				$data['products'] = $product = $this->products_model->get_by_id_product(array_column($post['product_items'], 'product_id'));
				 $message          = '';
            foreach ($product as $value) {
                if ((int) $value->quantity_number < 1) {
                    $message = $value->product_name.' is out of stock <br>';
                    continue;
                }
                if ((int) $post['product_items'][$value->id]['qty'] > (int) $value->quantity_number) {
                    $message = $value->product_name.' is only '.$value->quantity_number.' in stock <br>';
                }
            }

		    $billing_shipping = $this->clients_model->get_customer_billing_and_shipping_details($post['clientid']);
            $post             = array_merge($post, reset($billing_shipping));
			unset($post['billing_country']);
            unset($post['shipping_country']);
            $post['show_shipping_on_invoice'] = 'on';
            $post['number']                   = get_option('next_invoice_number');
            $order_data['order_date']         = $post['date']           = _d(date('Y-m-d'));
            $post['duedate']                  = _d(date('Y-m-d', strtotime('+'.get_option('invoice_due_after').' DAY', strtotime(date('Y-m-d')))));
            $post['show_quantity_as']         = 1;
           
            $payment_modes = $this->payment_modes_model->get();
            foreach ($payment_modes as $modes) {
                if ($modes['selected_by_default']) {
                    $post['allowed_payment_modes'][] = $modes['id'];
                }
            }
			unset($order_data['newitems']);
            unset($post['product_items']);
			
            $post['currency'] = $this->currencies_model->get_base_currency()->id;
            $id               = $this->invoices_model->add($post);			
            if ($id) {
                $res                      = $this->invoices_model->get($id);
                $order_data['status']     = $res->status;
				$order_data['datecreated'] = _d(date('Y-m-d', strtotime('+'.get_option('invoice_due_after').' DAY', strtotime(date('Y-m-d')))));
                $order_data['invoice_id'] = $id;
				$clientNote=$order_data['clientnote'];
				unset($order_data['billing_street']);
				unset($order_data['billing_city']);
				unset($order_data['billing_state']);
				unset($order_data['billing_zip']);
				unset($order_data['shipping_city']);
				unset($order_data['shipping_zip']);
				unset($order_data['shipping_state']);
				unset($order_data['shipping_street']);
				unset($order_data['date']);
				unset($order_data['duedate']);
				unset($order_data['number']);
				unset($order_data['show_shipping_on_invoice']);
				unset($order_data['show_quantity_as']);
				unset($order_data['currency']);
				unset($order_data['billing_country']);
				unset($order_data['allowed_payment_modes']);
				unset($order_data['clientnote']);
				unset($order_data['tags']);
                $this->order_model->add_order($order_data);
				
				//lets mark the invoice as paid
				
				
				$payData=array(
				'invoiceid'=>$id,
				'amount'=>$post['total'],
				'date'=>$post['date'],
				'paymentmode'=>'stripe',
				'transactionid'=>$clientNote,
				'note'=>""
				);
				$this->Api_model->update_payment($id, $payData);
				
				 $message = array(
                'status' => TRUE,
                'message' => 'Order  added successful.'
                );
                $this->response($message, REST_Controller::HTTP_OK);
              
            }else{
				 $message = array(
                'status' => FALSE,
                'message' => 'Order add fail.'
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
				
			}
            
			
			
			 }
			

           
        }
    }


    /**
     * @api {delete} api/delete/leads/:id Delete a Lead
     * @apiName DeleteLead
     * @apiGroup Lead
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {Number} id lead unique ID.
     *
     * @apiSuccess {String} status Request status.
     * @apiSuccess {String} message Lead Delete Successful.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Lead Delete Successful."
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Lead Delete Fail.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Lead Delete Fail."
     *     }
     */
    public function data_delete($id = '')
    { 
        $id = $this->security->xss_clean($id);
        if(empty($id) && !is_numeric($id))
        {
            $message = array(
            'status' => FALSE,
            'message' => 'Invalid Lead ID'
        );
        $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        }
        else
        {
            // delete data
            $this->load->model('leads_model');
            $output = $this->leads_model->delete($id);
            if($output === TRUE){
                // success
                $message = array(
                'status' => TRUE,
                'message' => 'Lead Delete Successful.'
                );
                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                // error
                $message = array(
                'status' => FALSE,
                'message' => 'Lead Delete Fail.'
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }


    /**
     * @api {put} api/leads/:id Update a lead
     * @apiName PutLead
     * @apiGroup Lead
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {String} source            Mandatory Lead source.
     * @apiParam {String} status            Mandatory Lead Status.
     * @apiParam {String} name              Mandatory Lead Name.
     * @apiParam {String} [assigned]        Optional Lead assigned.
     * @apiParam {String} [client_id]       Optional Lead From Customer.
     * @apiParam {String} [tags]            Optional Lead tags.
     * @apiParam {String} [contact]         Optional Lead contact.
     * @apiParam {String} [title]           Optional Position.
     * @apiParam {String} [email]           Optional Lead Email Address.
     * @apiParam {String} [website]         Optional Lead Website.
     * @apiParam {String} [phonenumber]     Optional Lead Phone.
     * @apiParam {String} [company]         Optional Lead company.
     * @apiParam {String} [address]         Optional Lead address.
     * @apiParam {String} [city]            Optional Lead City.
     * @apiParam {String} [state]           Optional Lead state.
     * @apiParam {String} [country]         Optional Lead Country.
     * @apiParam {String} [default_language]        Optional Lead Default Language.
     * @apiParam {String} [description]             Optional Lead description.
     * @apiParam {String} [lastcontact]             Optional Lead Last Contact.
     * @apiParam {String} [is_public]               Optional Lead google sheet id.
     *
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *       "name": "Lead name",
     *       "contact": "contact",
     *       "title": "title",
     *       "company": "C.TY TNHH TM VẬN TẢI & DU LỊCH ĐẠI BẢO AN",
     *       "description": "description",
     *       "tags": "",
     *       "city": "London",
     *       "state": "London",
     *       "address": "1a The Alexander Suite Silk Point",
     *       "assigned": "5",
     *       "source": "4",
     *       "email": "AA@gmail.com",
     *       "website": "www.themesic.com",
     *       "phonenumber": "123456789",
     *       "is_public": "on",
     *       "default_language": "english",
     *       "client_id": "3",
     *       "lastcontact": "25/07/2019 08:38:04"
     *   }
     *
     * @apiSuccess {Boolean} status Request status.
     * @apiSuccess {String} message Lead Update Successful.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "status": true,
     *       "message": "Lead Update Successful."
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message Lead Update Fail.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "Lead Update Fail."
     *     }
     */
    public function data_put($id = '')
    {
        $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
        $this->form_validation->set_data($_POST);
        
        if(empty($id) && !is_numeric($id))
        {
            $message = array(
            'status' => FALSE,
            'message' => 'Invalid Lead ID'
            );
            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        }
        else
        {

            $update_data = $this->input->post();
            // update data
            $this->load->model('leads_model');
            $output = $this->leads_model->update($update_data, $id);
            if($output > 0 && !empty($output)){
                // success
                $message = array(
                'status' => TRUE,
                'message' => 'Lead Update Successful.'
                );
                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                // error
                $message = array(
                'status' => FALSE,
                'message' => 'Lead Update Fail.'
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    function handle_lead_attachments_array($leadid, $index_name = 'file')
    {
        $path           = get_upload_path_by_type('lead') . $leadid . '/';
        $CI             = &get_instance();

        if (isset($_FILES[$index_name]['name'])
            && ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
            if (!is_array($_FILES[$index_name]['name'])) {
                $_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
                $_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
                $_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
                $_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
                $_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
            }

            _file_attachments_index_fix($index_name);
            for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
                // Get the temp file path
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
                        || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
                        continue;
                    }

                    _maybe_create_upload_path($path);
                    $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                    $newFilePath = $path . $filename;

                    // Upload the file into the temp dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $CI = & get_instance();
                        $CI->load->model('leads_model');
                        $data   = [];
                        $data[] = [
                            'file_name' => $filename,
                            'filetype'  => $_FILES[$index_name]['type'][$i],
                            ];
                        $CI->leads_model->add_attachment_to_database($leadid, $data, false);
                    }
                }
            }
        }
        return true;
    }

}
