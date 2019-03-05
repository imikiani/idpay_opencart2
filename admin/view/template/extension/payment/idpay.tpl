<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-idpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) : ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) : ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php endif; ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form id="form-idpay" method="post" enctype="multipart/form-data" action="<?php echo $action; ?>" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="idpay_status"><?php echo $text_status; ?></label>
                <div class="col-sm-10">
                  <select name="idpay_status" class="form-control">
                    <?php if ($idpay_status) : ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                    <?php else : ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="idpay_api_key"><?php echo $text_api_key; ?></label>
                <div class="col-sm-10">
                  <input name="idpay_api_key" type="text" value="<?php echo $idpay_api_key; ?>" class="form-control" />
                  <?php if ($error_api_key) : ?>
                    <div class="text-danger"><?php echo $error_api_key; ?></div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sandbox"><?php echo $text_sandbox ?></label>
                <div class="col-sm-10">
                  <select name="idpay_sandbox" id="input-sandbox" class="form-control">
                    <option value="no" <?php if ($idpay_sandbox == 'no') echo 'selected="selected"'?>><?php echo $entry_sandbox_no ?></option>
                    <option value="yes" <?php if ($idpay_sandbox == 'yes') echo 'selected="selected"'?>><?php echo $entry_sandbox_yes ?></option>
                  </select>
                  <span><?php echo $text_sandbox_help ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="idpay_payment_successful_message"><?php echo $text_success_message; ?></label>
                <div class="col-sm-10">
                  <textarea name="idpay_payment_successful_message" class="form-control"><?php echo empty($idpay_payment_successful_message) ? $entry_payment_successful_message_default : $idpay_payment_successful_message; ?></textarea>
                  <span><?php echo $text_successful_message_help ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="idpay_payment_failed_message"><?php echo $text_failed_message; ?></label>
                <div class="col-sm-10">
                  <textarea name="idpay_payment_failed_message" class="form-control"><?php echo empty($idpay_payment_failed_message) ? $entry_payment_failed_message_default : $idpay_payment_failed_message; ?></textarea>
                  <span><?php echo $text_failed_message_help ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="idpay_order_status_id"><?php echo $text_order_status; ?></label>
                <div class="col-sm-10">
                  <select name="idpay_order_status_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) : ?>
                      <?php if ($order_status['order_status_id'] == $idpay_order_status_id) : ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php else : ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="idpay_sort_order"><?php echo $text_sort_order; ?></label>
                <div class="col-sm-10">
                  <input name="idpay_sort_order" type="text" value="<?php echo $idpay_sort_order; ?>" class="form-control" />
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>