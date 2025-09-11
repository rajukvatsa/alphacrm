<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo htmlspecialchars($title); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open_multipart($this->uri->uri_string()); ?>
                        <?php echo render_input('product_name', 'product_name', $product->product_name ?? ''); ?>
                        <?php echo render_textarea('product_description', 'product_description', $product->product_description ?? ''); ?>
                        <div class="row">
                          <div class="col-md-6">
                               <?php echo render_select('product_category_id', $product_categories, ['p_category_id', 'p_category_name'], 'products_categories', !empty(set_value('product_category_id')) ? set_value('product_category_id') : $product->product_category_id ?? ''); ?>
                            </div>
                            <div class="col-md-3">
                               <?php echo render_input('rate', _l('invoice_item_add_edit_rate_currency'), $product->rate ?? '', 'number'); ?>
                            </div>
                            <div class="col-md-3">
                              <?php echo render_input('quantity_number', 'quantity', $product->quantity_number ?? '', 'number'); ?>
                            </div>
                        </div>
                        <?php
                          $existing_image_class = 'col-md-4';
                          $input_file_class     = 'col-md-8';
                          if (empty($product->product_image)) {
                              $existing_image_class = 'col-md-12';
                              $input_file_class     = 'col-md-12';
                          }
                        ?>
                        <div class="row">
                          <?php if (!empty($product->product_image)) { ?>
                          <div class="<?php echo htmlspecialchars($existing_image_class); ?>">
                              <div class="existing_image">
                                <label class="control-label">Existing Image</label>
                                <img src="<?php echo base_url('modules/'.PRODUCTS_MODULE_NAME.'/uploads/'.$product->product_image); ?>" class="img img-responsive img-thubnail zoom"/>
                              </div>
                          </div>
                          <?php } ?>
                          <div class="<?php echo htmlspecialchars($input_file_class); ?>">
                              <div class="attachment">
                                <div class="form-group">
                                  <label for="attachment" class="control-label"><small class="req text-danger">* </small><?php echo _l('product_image'); ?></label>
                                  <input type="file" extension="png,jpg,jpeg,gif" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="product" id="product" required>
                                </div>
                              </div>
                          </div>
                        </div>
                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    var mode = '<?php echo '' . $this->uri->segment(3, 0); ?>';
    (mode == 'add_product') ? $('input[type="file"]').prop('required',true) : $('input[type="file"]').prop('required',false);
  $(function () {
    appValidateForm($('form'), {
      product_name        : "required",
      product_description : "required",
      product_category_id : "required",
      rate                : "required",
      quantity_number     : "required"
    });
  });
</script>