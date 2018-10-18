<?php echo $this->Form->create("Table", array("class" => "form-horizontal form-separate", "action" => "edit", "id" => "formSubmit", "inputDefaults" => array("error" => array("attributes" => array("wrap" => "label", "class" => "error"))))) ?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="block-inner text-danger">
                    <h6 class="heading-hr"><?= __("Ubah Meja") ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table width="100%" class="table">
                        <div class="panel-heading" style="background:#2179cc">
                            <h6 class="panel-title" style=" color:#fff"><i class="icon-menu2"></i><?= __("Data Meja") ?></h6>
                        </div>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Table.name", __("Nama"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Table.name", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'required'));
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                            echo $this->Form->label("Table.pos_x", __("Posisi X"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Table.pos_x", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control text-right", 'required', 'data-pos-x' => $this->data['Table']['pos_x']));
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                            echo $this->Form->label("Table.pos_y", __("Posisi Y"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Table.pos_y", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control text-right", 'required', 'data-pos-y' => $this->data['Table']['pos_y']));
                                            ?>
                                        </div>
                                        <input type="hidden" name="data[Table][is_pos_change]" value="0" id="flagPos">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-sm-3 col-md-4 control-label">
                                                <label>Label</label>
                                            </div>
                                            <div class="col-sm-9 col-md-8">
                                                <input type="text" class="form-control" name="data[Table][label]" required maxlength="2" onkeyup="this.value = this.value.toUpperCase()" value="<?= !empty($this->data['Table']['label']) ? $this->data['Table']['label'] : "" ?>">
                                                <span class="help-block" id="limit-text">Label ini digunakan pada 3D Render Layout Meja. Contoh : M1 -> Meja-1</span>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            echo $this->Form->label("Table.note", __("Keterangan"), array("class" => "col-sm-2 control-label"));
                                            echo $this->Form->input("Table.note", array("div" => array("class" => "col-sm-10"), "label" => false, "class" => "ckeditor-fix"));
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

<script>
    $(document).ready(function () {
        var pos_x = $("#TablePosX").data("pos-x");
        var pos_y = $("#TablePosY").data("pos-y");
        var value = 0;
        $("#TablePosX").on("change keyup", function () {
            if ($(this).val() != pos_x) {
                value = 1;
            }
            if ($(this).val() == pos_x && $("#TablePosY").val() == pos_y) {
                value = 0;
            }
            $("#flagPos").val(value);
        });

        $("#TablePosY").on("change", function () {
            if ($(this).val() != pos_y) {
                value = 1;
            }
            if ($(this).val() == pos_y && $("#TablePosX").val() == pos_x) {
                value = 0;
            }
            $("#flagPos").val(value);
        });
    });
</script>