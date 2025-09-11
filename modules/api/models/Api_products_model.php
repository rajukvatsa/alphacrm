<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Api_products_model extends Api_model
{
	public function __construct()
    {
        parent::__construct();
    }

    public function get($id='')
    {
    	$this->db->select(db_prefix().'product_master.id as id,product_name,product_sku,product_description,rate,quantity_number,product_image,product_shipping_charge,product_tax,p_category_id,p_category_name,p_category_parent,p_category_level');
    	$this->db->join('product_assign_categories', db_prefix().'product_assign_categories.product_id='.db_prefix().'product_master.id', 'LEFT');
    	$this->db->join('product_categories', db_prefix().'product_categories.p_category_id ='.db_prefix().'product_assign_categories.category_id','LEFT');
    	$this->db->where(db_prefix().'product_categories.p_category_parent','0');
    	$this->db->or_where(db_prefix().'product_categories.p_category_parent');
    	$this->db->group_by(db_prefix().'product_master.id');
    	$this->db->order_by(db_prefix().'product_master.id','DESC');
        if ($id) {
            $this->db->where_in('id', $id);
            if (is_array($id)) {
                $product = $this->db->get(db_prefix().'product_master')->result_array();
            } else {
                $product = $this->db->get(db_prefix().'product_master')->row_array();
            }
            return $product;
        }
        //$this->db->order_by()
        $products = $this->db->get(db_prefix().'product_master')->result_array();
        return $products;
    }

    /*public function data_search($search='')
    {
    	if($search != ''){
            $q = $search;
            $q = trim($q);
            echo $q;
        }
        die;
    }*/

    public function get_product_images($id)
    {
    	$this->db->where('product_id',$id);
    	return $this->db->get(db_prefix().'product_images')->result_array();
    }

    public function delete($id)
    {
    	$this->db->join('product_assign_categories', db_prefix().'product_assign_categories.product_id='.db_prefix().'product_master.id', 'LEFT');
    	$this->db->join('tblproduct_images', db_prefix().'tblproduct_images.product_id='.db_prefix().'product_master.id', 'LEFT');
    	$this->db->where(db_prefix().'product_master.id',$id);
    	$this->db->delete(db_prefix().'product_master');
    	return true;
    }
}