<?php echo $this->Form->create("Order", array("class" => "form-horizontal form-separate", "action" => "edit", "id" => "formSubmit", "inputDefaults" => array("error" => array("attributes" => array("wrap" => "label", "class" => "error"))))) ?>
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
                                            echo $this->Form->input("Order.table_id", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "select-full", 'empty' => '', 'placeholder' => '- Pilih Meja -'));
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
                                <th width="2%"><?= __("No") ?></th>
                                <th width="20%"><?= __("Nama Makanan/Minuman") ?></th>
                                <th width="15%"><?= __("Harga Satuan") ?></th>
                                <th width="10%"><?= __("Jumlah") ?></th>
                                <th width="28%"><?= __("Keterangan") ?></th>
                                <th width="20%"><?= __("Total") ?></th>
                                <th width="5%"><?= __("Aksi") ?></th>
                            </tr>
                        </thead>
                        <tbody id="target-detail-order">
                            <?php
                            $no = 1;
                            if (!empty($this->data['OrderDetail'])) {
                                foreach ($this->data['OrderDetail'] as $i => $detail) {
                                    ?>
                                    <tr>
                                        <td class="text-center nomorIdx"><?= $no ?></td>
                                        <td class="text-center"><?= $this->Form->input("OrderDetail.$i.resto_menu_id", ['div' => false, 'label' => false, 'class' => 'select-full', 'options' => $restoMenus, 'empty' => "", 'plcaeholder' => "- Pilih Menu -", 'id' => "menu$i", 'onchange' => "get_harga_satuan($i)", 'required']) ?></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">Rp.</button>
                                                </span>
                                                <input type="text" class="form-control text-right" name="data[OrderDetail][<?= $i ?>][amount]" id="hargaSatuan<?= $i ?>" value="<?= ic_rupiah($detail['amount']) ?>" readonly>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">,00.</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control text-right" name="data[OrderDetail][<?= $i ?>][quantity]" id="quantity<?= $i ?>" value="<?= ic_rupiah($detail['quantity']) ?>" onkeyup="calculate_total_per_item(<?= $i ?>)">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">pcs</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td><input type="text" class="form-control" name="data[OrderDetail][<?= $i ?>][note]" value="<?= $detail['note'] ?>"></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">Rp.</button>
                                                </span>
                                                <input type="text" class="form-control text-right totalperitem" name="data[OrderDetail][<?= $i ?>][total]" id="total<?= $i ?>" value="<?= ic_rupiah($detail['total']) ?>" readonly>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">,00.</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(false)" onclick="deleteThisRow($(this))"><i class="icon-remove3"></i></a>
                                        </td>
                                    </tr>                                    
                                    <?php
                                    $no++;
                                }
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <a class="text-success numN" href="javascript:void(false)" onclick="addThisRow($(this), 'detail-order')" data-n="<?= $no - 1 ?>"><i class="icon-plus-circle"></i></a>
                                    </td>
                                </tr>
                                <?php
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7">Tidak Ada Data</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Grand Total</strong></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button">Rp.</button>
                                        </span>
                                        <input type="text" class="form-control text-right" name="data[Order][grand_total]" id="grandTotal" value="<?= ic_rupiah($this->data['Order']['grand_total']) ?>" readonly>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button">,00.</button>
                                        </span>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="text-center">
                    <input name="Button" type="button" onclick="history.go(-1);" class="btn btn-success" value="<?= __("Kembali") ?>">
                    <input type="reset" value="Reset" class="btn btn-info">
                    <input type="submit" value="Simpan" class="btn btn-danger">
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end() ?>
<script>
    var resto_menus = <?= $this->Engine->toJSONoptionsWithParent($restoMenus, true) ?>;
    $(document).ready(function () {

    });

    function get_harga_satuan(n) {
        var resto_menu_id = $("#menu" + n).val();
        if (resto_menu_id != "" && resto_menu_id != null) {
            $.ajax({
                url: BASE_URL + "/admin/resto_menus/get_data_resto_menu/" + resto_menu_id,
                type: "GET",
                dataType: "JSON",
                data: {},
                success: function (response) {
                    if (response.status == 206) {
                        $("#hargaSatuan" + n).val(IDR(response.data.RestoMenu.price));
                        calculate_total_per_item(n);
                    }
                }
            });
        }
    }

    function calculate_total_per_item(n) {
        var harga_satuan = $("#hargaSatuan" + n).val();
        var quantity = parseInt($("#quantity" + n).val());
        if (harga_satuan != null && quantity != null) {
            harga_satuan = parseInt(replaceAll(harga_satuan, ".", ""));
            var total = harga_satuan * quantity;
            $("#total" + n).val(IDR(total));
            calculate_grand_total();
        }
    }

    function calculate_grand_total() {
        var grand_total = 0;
        $.each($(".totalperitem"), function () {
            var total_per_item = parseInt(replaceAll($(this).val(), ".", ""));
            grand_total += total_per_item;
        });
        $("#grandTotal").val(IDR(grand_total));
    }

    function deleteThisRow(e) {
        var tbody = $(e).parents("tbody");
        e.parents("tr").remove();
        fixNumber(tbody);
        calculate_grand_total();
    }

    function addThisRow(e, t, optFunc) {
        var n = $(e).data("n");
        var template = $('#tmpl-' + t).html();
        Mustache.parse(template);
        var options = {
            i: 2,
            n: n,
            resto_menus: resto_menus
        };
        if (typeof (optFunc) !== 'undefined') {
            $.extend(options, window[optFunc]());
        }
        var rendered = Mustache.render(template, options);
        $('#target-' + t + " tr:last").before(rendered);
        $(e).data("n", n + 1);
        reloadSelect2();
        fixNumber($(e).parents("tbody"));
    }

    function fixNumber(e) {
        var i = 1;
        $.each(e.find("tr"), function () {
            $(this).find(".nomorIdx").html(i);
            i++;
        })
    }
</script>

<script type="x-tmpl-mustache" id="tmpl-detail-order">
    <tr>
        <td class="text-center nomorIdx">{{i}}</td>
        <td>
            <select name="data[OrderDetail][{{n}}][resto_menu_id]" class="select-full" id="menu{{n}}" onchange="get_harga_satuan({{n}})" required>
                <option value=''>- Pilih Menu -</option>
                {{#resto_menus}}
                <optgroup label="{{parent}}">
                {{#child}}
                <option value="{{value}}">{{label}}</option>
                {{/child}}
                </optgroup>
                {{/resto_menus}}
            </select>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">Rp.</button>
                </span>
                <input type="text" class="form-control text-right" name="data[OrderDetail][{{n}}][amount]" id="hargaSatuan{{n}}" readonly value="0">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">,00.</button>
                </span>
            </div>
        </td>
        <td>
            <div class="input-group">
                <input type="number" class="form-control text-right" name="data[OrderDetail][{{n}}][quantity]" id="quantity{{n}}" value="1" onkeyup="calculate_total_per_item({{n}})">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">pcs</button>
                </span>
            </div>
        </td>
        <td><input type="text" class="form-control" name="data[OrderDetail][{{n}}][note]"></td>
        <td>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">Rp.</button>
                </span>
                <input type="text" class="form-control text-right totalperitem" name="data[OrderDetail][{{n}}][total]" id="total{{n}}" readonly value="0">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">,00.</button>
                </span>
            </div>
        </td>
        <td class="text-center">
            <a href="javascript:void(false)" onclick="deleteThisRow($(this))"><i class="icon-remove3"></i></a>
        </td>
    </tr>
</script>