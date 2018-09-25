<?php echo $this->Form->create("Order", array("class" => "form-horizontal form-separate", "action" => "cashier", "id" => "formSubmit", "inputDefaults" => array("error" => array("attributes" => array("wrap" => "label", "class" => "error"))))) ?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="block-inner text-danger">
                    <h6 class="heading-hr"><?= __("Cetak Order") ?>
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
                                            echo $this->Form->label("Dummy.no_order", __("Nomor Order"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            ?>
                                            <div class="col-sm-9 col-md-8">
                                                <div class="has-feedback">
                                                    <input type="text" class="form-control typeahead-ajax" placeholder="Cari Nomor Order ..." id="NoOrder" onkeyup="no_order_input(this, '#OrderTableId')">
                                                    <input type="hidden" name="data[Order][id]" id="orderId">
                                                    <i class="icon-search3 form-control-feedback"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Order.table_id", __("Meja"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Order.table_id", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "select-full", 'empty' => '', 'placeholder' => '- Pilih Meja -', 'onchange' => 'table_input(this, "#NoOrder")'));
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
                                            echo $this->Form->label("Dummy.date", __("Tanggal Order"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Dummy.date", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'disabled'));
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            echo $this->Form->label("Dummy.name", __("Petugas"), array("class" => "col-sm-3 col-md-4 control-label"));
                                            echo $this->Form->input("Dummy.name", array("div" => array("class" => "col-sm-9 col-md-8"), "label" => false, "class" => "form-control", 'disabled'));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered stn-table" width="100%">
                        <div class="panel-heading" style="background:#2179cc">
                            <h6 class="panel-title" style=" color:#fff"><i class="icon-menu2"></i><?= __("Detail Order") ?></h6>
                        </div>
                        <thead>
                            <tr>
                                <th width="50"><?= __("No.") ?></th>
                                <th><?= __("Nama Menu") ?></th>
                                <th colspan="2"><?= __("Harga Satuan") ?></th>
                                <th width="5-"><?= __("Qty") ?></th>
                                <th colspan="2"><?= __("Total") ?></th>
                            </tr>
                        </thead>
                        <tbody id="target-detail-order">
                            <tr>
                                <td colspan="7" class="text-center">Tidak Ada Data.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Grand Total</strong></td>
                                <td class="text-center" style = "border-right-style:none !important; font-weight: bold;" width = "50">Rp.</td>
                                <td class="text-right grandTotal" style = "border-left-style:none !important; font-weight: bold;" width = "150">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>                
            </div>
            <div class="form-actions text-center">               
                <input name="Button" type="button" onclick="history.go(-1);" class="btn btn-success" value="<?= __("Kembali") ?>">
                <input type="reset" value="Reset" class="btn btn-info">
                <input type="button" value="Cetak" class="btn btn-primary" id="btnPrintReceipt">
                <input type="submit" value="<?= __("Selesai") ?>" class="btn btn-danger">
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end() ?>

<script>
    $(document).ready(function () {
        var orders = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('no_order'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: '<?= Router::url("/admin/orders/list_order", true) ?>',
            remote: {
                url: '<?= Router::url("/admin/orders/list_order", true) ?>' + '?q=%QUERY',
                wildcard: '%QUERY',
            }
        });
        orders.clearPrefetchCache();
        orders.initialize(true);
        $('input.typeahead-ajax').typeahead({
            hint: false,
            highlight: true
        }, {
            name: 'orders',
            display: 'no_order',
            source: orders.ttAdapter(),
            templates: {
                header: '<center><h5>Data Order</h5></center><hr>',
                suggestion: function (context) {
                    return '<p>Nomor : ' + context.no_order + '<br>Tanggal : ' + cvtWaktu(context.created) + '<br>Meja : ' + context.table_name + '</p>';
                },
                empty: [
                    '<center><h5>Data Order</h5></center><hr> <center><p> Hasil Pencarian Anda Tidak Dapat Ditemukan. </p></center>',
                ]
            }
        });
        $('input.typeahead-ajax').bind('typeahead:select', function (ev, suggestion) {
//            console.log(suggestion);
            $("#NoOrder").keyup();
            $('#orderId').val(suggestion.id);
            $('#OrderTableId').val(suggestion.table_name);
            $('#DummyDate').val(cvtWaktu(suggestion.created));
            $('#DummyName').val(suggestion.account_name);

            // data order detail process
            var template = "detail-order";
            var counter = 1;
            var grand_total = 0;
            $("#target-" + template).html("");
            $.each(suggestion.order_detail, function (index, value) {
                var menu_name = value.RestoMenu.name;
                var price_per_menu = IDR(value.RestoMenu.price);
                var quantity = value.quantity;
                var total = IDR(value.total);
                append_data(template, counter, menu_name, price_per_menu, quantity, total);
                counter++;
                grand_total += parseInt(value.total);
            });

            $(".grandTotal").html(IDR(grand_total));
        });
        
        $("#btnPrintReceipt").on("click", function() {
            var order_id = $("#orderId").val();
            if(order_id != "" && order_id != null) {
                print_receipt(order_id);
            } else {
                $.growl.warning({title: "Error", message: "Silahkan Pilih Nomor Order/Meja yang mau dicetak."});
            }
        });
    });

    function append_data(temp, no, menu_name, price_per_menu, quantity, total) {
        var template = $("#tmpl-" + temp).html();
        Mustache.parse(template);
        var options = {
            i: no,
            menu_name: menu_name,
            price_per_menu: price_per_menu,
            quantity: quantity,
            total: total
        };
        var rendered = Mustache.render(template, options);
        $("#target-" + temp).append(rendered);
    }

    function table_input(e, target) {
        var table_id = $(e).val();
        if (table_id != "" && table_id != null) {
            $(target).attr("disabled", 'disabled');
            get_data_order_by_table(table_id);
        } else {
            empty_detail_order();
            $(target).removeAttr("disabled");
        }
    }

    function no_order_input(e, target) {
        var text = $(e).val();
        if (text != "" && text != null) {
            $(target).attr("disabled", 'disabled');
        } else {
            empty_detail_order();
            $(target).removeAttr("disabled");
        }
    }

    function empty_detail_order() {
        $("#target-detail-order").html("");
        var template = $("#tmpl-no-data").html();
        Mustache.parse(template);
        var rendered = Mustache.render(template);
        $("#target-detail-order").append(rendered);
        $(".grandTotal").html("0");
        $("#orderId").val("");
        $("#DummyDate").val("");
        $("#DummyName").val("");
        $("#NoOrder").val("");
    }

    function get_data_order_by_table(table_id) {
        if (table_id != null && table_id != "") {
            $.ajax({
                url: BASE_URL + "/admin/orders/get_data_order_by_table/" + table_id,
                type: "GET",
                dataType: "JSON",
                data: {},
                success: function (response) {
                    if (response.status == 206) {
                        $("#orderId").val(response.data.Order.id);
                        $("#DummyDate").val(cvtWaktu(response.data.Order.created));
                        $("#DummyName").val(response.data.Account.Biodata.full_name);
                        $("#NoOrder").val(response.data.Order.no_order);

                        var template = "detail-order";
                        var counter = 1;
                        var grand_total = 0;
                        $("#target-" + template).html("");
                        $.each(response.data.OrderDetail, function (index, value) {
                            var menu_name = value.RestoMenu.name;
                            var price_per_menu = IDR(value.RestoMenu.price);
                            var quantity = value.quantity;
                            var total = IDR(value.total);
                            append_data(template, counter, menu_name, price_per_menu, quantity, total);
                            counter++;
                            grand_total += parseInt(value.total);
                        });

                        $(".grandTotal").html(IDR(grand_total));
                    } else {
                        empty_detail_order();
                    }
                }
            });
        }
    }
    
    function print_receipt(order_id) {
        if(order_id != "" && order_id != null) {            
            $.ajax({
                url: BASE_URL + "/admin/orders/print_receipt",
                type: "POST",
                dataType: "JSON",
                data: {
                    order_id: order_id
                },
                success: function(response) {
                    if(response.status == 206) {
                        $.growl.notice({title: "Sukses", message: data.message});
                    } else {
                        $.growl.warning({title: "Error", message: data.message});
                    }
                }
            });
        }
    }
</script>

<script type="x-tmpl-mustache" id="tmpl-detail-order">
    <tr>
    <td class="text-center nomorIdx">{{i}}</td>
    <td class="text-center">{{menu_name}}</td>
    <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
    <td class="text-right" style = "border-left-style:none !important" width = "150">{{price_per_menu}}</td>
    <td class="text-center">{{quantity}}</td>
    <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>
    <td class="text-right" style = "border-left-style:none !important" width = "150">{{total}}</td>
    </tr>
</script>

<script type="x-tmpl-mustache" id="tmpl-no-data">
    <tr>
    <td class="text-center" colspan="7">Tidak Ada Data.</td>       
    </tr>
</script>