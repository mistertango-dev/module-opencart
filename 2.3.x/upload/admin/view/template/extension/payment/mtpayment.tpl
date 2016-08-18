<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mister-tango" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
           class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mister-tango"
              class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-username"><span data-toggle="tooltip"
                                                                             title="<?php echo $help_username; ?>"><?php echo $entry_username; ?></span></label>

            <div class="col-sm-10">
              <input type="text" name="mtpayment_username" value="<?php echo $mtpayment_username; ?>"
                     placeholder="<?php echo $entry_username; ?>" id="input-username"
                     class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-secret-key"><span data-toggle="tooltip"
                                                                               title="<?php echo $help_secret_key; ?>"><?php echo $entry_secret_key; ?></span></label>

            <div class="col-sm-10">
              <input type="text" name="mtpayment_secret_key" value="<?php echo $mtpayment_secret_key; ?>"
                     placeholder="<?php echo $entry_secret_key; ?>" id="input-secret-key"
                     class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"
                   for="input-standard-redirect"><?php echo $entry_standard_redirect; ?></label>

            <div class="col-sm-10">
              <input id="input-standard-redirect" type="checkbox" name="mtpayment_standard_redirect"
                     value="1"<?php if ($mtpayment_standard_redirect): ?> checked="checked"<?php endif; ?> />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip"
                                                                          title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>

            <div class="col-sm-10">
              <input type="text" name="mtpayment_total" value="<?php echo $mtpayment_total; ?>"
                     placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"
                   for="input-order-pending-status"><?php echo $entry_order_pending_status; ?></label>

            <div class="col-sm-10">
              <select name="mtpayment_order_pending_status_id" id="input-order-pending-status"
                      class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $mtpayment_order_pending_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"
                        selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option
                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"
                   for="input-order-success-status"><?php echo $entry_order_success_status; ?></label>

            <div class="col-sm-10">
              <select name="mtpayment_order_success_status_id" id="input-order-success-status"
                      class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $mtpayment_order_success_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"
                        selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option
                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"
                   for="input-order-error-status"><?php echo $entry_order_error_status; ?></label>

            <div class="col-sm-10">
              <select name="mtpayment_order_error_status_id" id="input-order-error-status"
                      class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $mtpayment_order_error_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"
                        selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option
                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"
                   for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>

            <div class="col-sm-10">
              <select name="mtpayment_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $mtpayment_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                        selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>

            <div class="col-sm-10">
              <select name="mtpayment_status" id="input-status" class="form-control">
                <?php if ($mtpayment_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"
                   for="input-sort-order"><?php echo $entry_sort_order; ?></label>

            <div class="col-sm-10">
              <input type="text" name="mtpayment_sort_order" value="<?php echo $mtpayment_sort_order; ?>"
                     placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order"
                     class="form-control"/>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
