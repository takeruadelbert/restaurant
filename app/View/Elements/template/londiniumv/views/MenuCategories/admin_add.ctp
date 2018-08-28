<?php echo $this->Form->create("MenuCategory", array("class" => "form-horizontal form-separate", "action" => "add", "type" => "file" , "id" => "formSubmit", "inputDefaults" => array("error" => array("attributes" => array("wrap" => "label", "class" => "error"))))) ?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="block-inner text-danger">
                    <h6 class="heading-hr"><?= __("Tambah Kategori") ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table width="100%" class="table">
                        <div class="panel-heading" style="background:#2179cc">
                            <h6 class="panel-title" style=" color:#fff"><i class="icon-menu2"></i><?= __("Data Kategori") ?></h6>
                        </div>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("MenuCategory.name", __("Nama"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("MenuCategory.name", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'required'));
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("MenuCategory.uniq", __("Kode"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("MenuCategory.uniq", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'required', 'onkeydown' => 'toUpperCase(this)'));
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
                                            echo $this->Form->label("MenuCategory.parent_id", __("Parent"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("MenuCategory.parent_id", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "select-full", 'empty' => '', 'placeholder' => '- Pilih Parent -'));
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("MenuCategory.gambar", __("Thumbnail"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("MenuCategory.gambar", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control styled", 'required', "type" => "file"));
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
                                            echo $this->Form->label("MenuCategory.ordering_number", __("Ordering Number"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("MenuCategory.ordering_number", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control"));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-actions text-right">
                                    <input name="Button" type="button" onclick="history.go(-1);" class="btn btn-success" value="<?= __("Kembali") ?>">
                                    <input type="reset" value="Reset" class="btn btn-info">
                                    <input type="submit" value="<?= __("Simpan") ?>" class="btn btn-danger">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end() ?>