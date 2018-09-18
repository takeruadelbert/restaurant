<form action="#" role="form" class="panel-filter">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Filter Data</h6>
            <div class="panel-icons-group"> <a href="#" data-panel="collapse" class="btn btn-link btn-icon"><i class="icon-arrow-up9"></i></a></div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label><?= __("No. Order") ?></label>
                        <?= $this->Form->input(null, array("default" => isset($this->request->query['Order_no_order']) ? $this->request->query['Order_no_order'] : '', "name" => "Order.no_order", "div" => false, "label" => false, "class" => "form-control tip")) ?>
                    </div>
                    <div class="col-md-6">
                        <label><?= __("Meja") ?></label>
                        <?= $this->Form->input(null, array("default" => isset($this->request->query['select_Order_table_id']) ? $this->request->query['select_Order_table_id'] : '', "name" => "select.Order.table_id", "div" => false, "label" => false, "class" => "select-full", "empty" => "", 'placeholder' => '- Semua -', 'options' => $tables)) ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label><?= __("Periode Tanggal") ?></label>
                        <?= $this->Form->input(null, ['div' => false, 'label' => false, 'class' => "form-control datepicker", 'name' => 'awal.Order.created', 'default' => isset($this->request->query['awal_Order_created']) ? $this->request->query['awal_Order_created'] : "", 'placeholder' => "Periode Awal ..."]) ?>
                    </div>
                    <div class="col-md-3">
                        <label><?= __("&nbsp;") ?></label>
                        <?= $this->Form->input(null, ['div' => false, 'label' => false, 'class' => 'form-control datepicker', 'name' => "akhir.Order.created", 'default' => isset($this->request->query['akhir_Order_created']) ? $this->request->query['akhir_Order_created'] : "", 'placeholder' => "Periode Akhir ..."]) ?>
                    </div>
                    <div class="col-md-6">
                        <label><?= __("Device") ?></label>
                        <?= $this->Form->input(null, ['div' => false, 'label' => false, 'class' => 'select-full', 'name' => 'select.Order.device_id', 'default' => isset($this->request->query['select_Order_device_id']) ? $this->request->query['select_Order_device_id'] : "", 'empty' => "", 'placeholder' => '- Semua -', 'options' => $devices]) ?>
                    </div>
                </div>
            </div>
            <div class="form-actions text-center">
                <input type="button" value="<?= __("Reset") ?>" class="btn btn-danger btn-filter-reset">
                <input type="button" value="<?= __("Cari") ?>" class="btn btn-info btn-filter">
            </div>
        </div>
    </div>
</form>
<script>
    filterReload();
</script>
