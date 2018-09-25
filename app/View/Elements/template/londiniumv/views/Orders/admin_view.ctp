<?php echo $this->Form->create("Order", array("class" => "form-horizontal form-separate", "action" => "view", "id" => "formSubmit", "inputDefaults" => array("error" => array("attributes" => array("wrap" => "label", "class" => "error"))))) ?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="block-inner text-danger">
                    <h6 class="heading-hr"><?= __("Lihat Data Order") ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table width="100%" class="table">
                        <div class="panel-heading" style="background:#2179cc">
                            <h6 class="panel-title" style=" color:#fff"><i class="icon-menu2"></i><?= __("Data Order") ?></h6>
                        </div>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Order.no_order", __("Nomor Order"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Order.no_order", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'disabled'));
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Order.table_id", __("Meja"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Order.table_id", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "select-full", 'disabled'));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Order.created", __("Tanggal Order"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Order.created", array("type" => "text", "div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control datetime", 'disabled'));
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Account.Biodata.full_name", __("Petugas"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Account.Biodata.full_name", array("type" => "text", "div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'disabled'));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="table-responsive stn-table">
                    <table width="100%" class="table table-hover table-bordered">
                        <div class="panel-heading" style="background:#2179cc">
                            <h6 class="panel-title" style=" color:#fff"><i class="icon-menu2"></i><?= __("Detail Order") ?></h6>
                        </div>
                        <thead>
                            <tr>
                                <th width="50"><?= __("No") ?></th>
                                <th><?= __("Nama Makanan/Minuman") ?></th>
                                <th colspan="2"><?= __("Harga Satuan") ?></th>
                                <th width="100" colspan="2"><?= __("Jumlah") ?></th>
                                <th><?= __("Keterangan") ?></th>
                                <th colspan="2"><?= __("Total") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($this->data['OrderDetail'])) {
                                $no = 1;
                                foreach ($this->data['OrderDetail'] as $detail) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no ?></td>
                                        <td class="text-center"><?= $detail['RestoMenu']['name'] ?></td>
                                        <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
                                        <td class="text-right" style = "border-left-style:none !important" width = "150"><?= ic_rupiah($detail['amount']) ?></td>
                                        <td class="text-right" style = "border-right-style:none !important" width = "150"><?= $detail['quantity'] ?></td>
                                        <td class="text-center" style = "" width = "50">pcs</td>
                                        <td class="text-center"><i><?= emptyToStrip(@$detail['note']) ?></i></td>
                                        <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
                                        <td class="text-right" style = "border-left-style:none !important" width = "150"><?= ic_rupiah($detail['total']) ?></td>
                                    </tr>
                                    <?php
                                    $no++;
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="9">Tidak Ada Data</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-right"><strong>SubTotal</strong></td>
                                <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
                                <td class="text-right" style = "border-left-style:none !important" width = "150"><strong><?= ic_rupiah($this->data['Order']['total_kotor']) ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right"><strong>PPN</strong></td>
                                <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
                                <td class="text-right" style = "border-left-style:none !important" width = "150"><?= ic_rupiah($this->data['Order']['ppn']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right"><strong>Grand Total</strong></td>
                                <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
                                <td class="text-right" style = "border-left-style:none !important" width = "150"><strong><?= ic_rupiah($this->data['Order']['grand_total']) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end() ?>