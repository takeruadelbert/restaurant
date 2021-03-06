<?php

App::uses('AppController', 'Controller');

class OrdersController extends AppController {

    var $name = "Orders";
    var $disabledAction = array(
    );
    var $contain = array(
        "Table",
        "OrderDetail" => [
            "RestoMenu" => [
                "MenuCategory"
            ]
        ],
        "Account" => [
            "Biodata"
        ],
        "OrderStatus"
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->_setPageInfo("admin_index", "");
        $this->_setPageInfo("admin_add", "");
        $this->_setPageInfo("admin_edit", "");
    }

    function beforeRender() {
        $this->options();
        parent::beforeRender();
    }

    function options() {
        $this->set("tables", ClassRegistry::init("Table")->find("list", ['fields' => ['Table.id', 'Table.name']]));
        $this->set("accounts", ClassRegistry::init("Account")->get_list_full_name());
        $this->set("orderStatuses", ClassRegistry::init("OrderStatus")->find("list", ['fields' => ['OrderStatus.id', 'OrderStatus.name']]));
        $this->set("restoMenus", ClassRegistry::init("RestoMenu")->get_list_menu());
    }

    function api_post_order() {
        $this->autoRender = false;
        //$data = json_decode(file_get_contents('php://input'), true);
        if ($this->request->is("POST")) {
            $data = json_decode($this->data['order'], true);
            if (!empty($data)) {
                // check if the device is registered by the system
                $account_id = $data['account_id'];
                if (!empty($account_id)) {
                    // check the print setting : 1 -> print order to kitchen, otherwise not print
                    $print_type = (int) $data['print_type'];
                    if (!empty($print_type)) {
                        // check the no table if it's registered on database
                        $no_table = $data['no_table'];
                        $dataTable = ClassRegistry::init("Table")->findByName($no_table);
                        if (!empty($dataTable)) {
                            $table_id = $dataTable['Table']['id'];
                            $no_order = ClassRegistry::init("Order")->generate_order_number();
                            $total_amount_before_tax_discount = $data['total'];
                            $grand_total = $total_amount_before_tax_discount;
                            $order_detail = [];
                            if (!empty($data['detail'])) {
                                $request_data_detail = json_decode($data['detail'], true);
                                foreach ($request_data_detail as $detail) {
                                    $order_detail[] = [
                                        "resto_menu_id" => @$detail['resto_menu_id'],
                                        "quantity" => @$detail['quantity'],
                                        "amount" => @$detail['amount'],
                                        "note" => @$detail['note'],
                                        "total" => $detail['amount'] * $detail['quantity']
                                    ];
                                }
                            }
                            $result = [
                                "Order" => [
                                    "no_order" => $no_order,
                                    "table_id" => $table_id,
                                    "total_kotor" => $total_amount_before_tax_discount,
                                    "grand_total" => $grand_total,
                                    "account_id" => $account_id
                                ],
                                "OrderDetail" => $order_detail
                            ];
                            $operator_name = ClassRegistry::init("Account")->get_name($account_id);
                            try {
                                $this->Order->saveAll($result);
                                if ($print_type == 1) {
                                    try {
                                        $dt = date("Y-m-d H:i:s");
                                        $dataSystemConfig = ClassRegistry::init("EntityConfiguration")->find("first");
                                        $ip_address_printer = $dataSystemConfig['EntityConfiguration']['ip_address_printer'];
                                        if (!empty($ip_address_printer)) {
                                            $dataLastInsertOrder = $this->Order->find("first", [
                                                "conditions" => [
                                                    "Order.id" => $this->Order->getLastInsertID()
                                                ],
                                                "contain" => [
                                                    "OrderDetail" => [
                                                        "RestoMenu"
                                                    ],
                                                    "Table"
                                                ]
                                            ]);
                                            if (!empty($dataLastInsertOrder)) {
                                                $this->print_order($ip_address_printer, $dataLastInsertOrder['Table']['name'], $dataLastInsertOrder['OrderDetail'], $dt, $operator_name);
                                                return json_encode($this->_generateStatusCode(200, "Data order has been saved and printed successfully."));
                                            } else {
                                                debug("Error : Last Insert Order ID not found.");
                                                return json_encode($this->_generateStatusCode(401, "Error : Last Insert Order ID not found."));
                                            }
                                        } else {
                                            debug("Error : IP Address Printer not found.");
                                            return json_encode($this->_generateStatusCode(401, "Error : IP Address Printer not found."));
                                        }
                                    } catch (Exception $ex) {
                                        debug("Error : print order failed.");
                                        return json_encode($this->_generateStatusCode(405, "Error : print order failed."));
                                    }
                                } else {
                                    return json_encode($this->_generateStatusCode(200, "Data has been saved successfully."));
                                }
                            } catch (Exception $ex) {
                                debug("Error : failed to save.");
                                return json_encode($this->_generateStatusCode(405, "Error : Failed to save."));
                            }
                        } else {
                            return json_encode($this->_generateStatusCode(401, "No Table found."));
                        }
                    } else {
                        return json_encode($this->_generateStatusCode(401, "Print Type Value is invalid."));
                    }
                } else {
                    return json_encode($this->_generateStatusCode(401, "Invalid 'Account ID' param."));
                }
            } else {
                return json_encode($this->_generateStatusCode(401, "failed"));
            }
        } else {
            return json_encode($this->_generateStatusCode(405, "Invalid Request Type."));
        }
    }

    function api_get_order_status() {
        $this->autoRender = false;
        $view = new View($this);
        $helper = $view->loadHelper("Html");
        if ($this->request->is("GET")) {
            $account_id = $this->request->query['account_id'];
            if (!empty($account_id)) {
                $dataOrder = $this->{Inflector::classify($this->name)}->find("all", [
                    "conditions" => [
                        "Order.account_id" => $account_id
                    ],
                    "contain" => [
                        "Table",
                        "OrderStatus"
                    ],
                    "order" => "Order.created DESC"
                ]);
                $result = [];
                if (!empty($dataOrder)) {
                    foreach ($dataOrder as $order) {
                        $result[] = [
                            "order_id" => $order['Order']['id'],
                            "no_order" => $order['Order']['no_order'],
                            "order_status_id" => $order['Order']['order_status_id'],
                            "order_date" => $helper->cvtWaktu($order['Order']['created'])
                        ];
                    }
                    return json_encode($this->_generateStatusCode(206, 'OK', $result));
                } else {
                    return json_encode($this->_generateStatusCode(400, 'Invalid Data Order.'));
                }
            } else {
                return json_encode($this->_generateStatusCode(405, "Error : invalid 'Account ID' param."));
            }
        } else {
            return json_encode($this->_generateStatusCode(405, "Invalid Request Type."));
        }
    }

    function admin_print_order() {
        if ($this->request->is("POST")) {
            $order_id = $this->data['Order']['id'];
            $dataOrder = $this->Order->find("first", [
                "conditions" => [
                    "Order.id" => $order_id
                ],
                "contain" => [
                    "OrderDetail" => [
                        "RestoMenu"
                    ],
                    "Table"
                ]
            ]);
            $dataPrinter = ClassRegistry::init("EntityConfiguration")->find("first");
            $ip_address_printer = $dataPrinter['EntityConfiguration']['ip_address_printer'];
            $table = $dataOrder['Table']['name'];
            if ($this->print_order($ip_address_printer, $table, $dataOrder['OrderDetail'])) {
                $this->Session->setFlash(__("Order Berhasil dicetak."), 'default', array(), 'success');
            } else {
                $this->Session->setFlash(__("Ada Kesalahan Pada Sistem. Coba Periksa Kembali Konfigurasi Printer."), 'default', array(), 'danger');
            }
            $this->redirect(array('action' => 'admin_print_order'));
        }
    }

    function admin_list_order() {
        $this->autoRender = false;
        $conds = [];
        if (isset($this->request->query['q'])) {
            $q = $this->request->query['q'];
            $conds[] = array(
                "or" => array(
                    "Order.no_order like" => "%$q%"
            ));
        }
        $suggestions = ClassRegistry::init("Order")->find("all", array(
            "conditions" => [
                $conds,
                "Order.order_status_id" => 1 // not completed yet
            ],
            "contain" => array(
                "Table",
                "Account" => [
                    "Biodata"
                ],
                "OrderDetail" => [
                    "RestoMenu"
                ]
            ),
            "limit" => 10,
        ));
        $result = [];
        foreach ($suggestions as $item) {
            if (!empty($item['Order'])) {
                $result[] = [
                    "id" => @$item['Order']['id'],
                    "no_order" => @$item['Order']['no_order'],
                    "account_name" => @$item['Account']['Biodata']['full_name'],
                    "created" => @$item['Order']['created'],
                    "table_name" => @$item['Table']['name'],
                    "order_detail" => @$item['OrderDetail']
                ];
            }
        }
        return json_encode($result);
    }

    function admin_change_order() {
        $this->conds = [
            "Order.order_status_id" => 1 // not completed yet
        ];
        parent::admin_index();
    }

    function admin_edit($id = null) {
        if (!$this->{ Inflector::classify($this->name) }->exists($id)) {
            throw new NotFoundException(__('Data tidak ditemukan'));
        } else {
            if ($this->request->is("post") || $this->request->is("put")) {
                $this->{ Inflector::classify($this->name) }->set($this->data);
                $this->{ Inflector::classify($this->name) }->data[Inflector::classify($this->name)]['id'] = $id;
                if ($this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('validate' => 'only', "deep" => true))) {
                    if (!is_null($id)) {
                        $this->{Inflector::classify($this->name)}->_deleteableHasmany();
                        $this->{Inflector::classify($this->name)}->_numberSeperatorRemover();
                        $this->{Inflector::classify($this->name)}->data['Order']['account_id'] = $this->Session->read("credential.admin.Account.id");
                        $this->{Inflector::classify($this->name)}->data['Order']['total_kotor'] = $this->{Inflector::classify($this->name)}->data['Order']['grand_total'];
                        $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                        $this->Session->setFlash(__("Data berhasil diubah"), 'default', array(), 'success');
                        $this->redirect(array('action' => 'admin_change_order'));
                    }
                } else {
                    $this->request->data[Inflector::classify($this->name)]["id"] = $id;
                    $this->validationErrors = $this->{ Inflector::classify($this->name) }->validationErrors;
                }
            } else {
                $rows = $this->{ Inflector::classify($this->name) }->find("first", array(
                    'conditions' => array(
                        Inflector::classify($this->name) . ".id" => $id
                    ),
                    'recursive' => 2
                ));
                $this->data = $rows;
            }
        }
    }

    function admin_cashier() {
        if ($this->request->is("post")) {
            $this->{ Inflector::classify($this->name) }->set($this->data);
            if ($this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('validate' => 'only', "deep" => true))) {
                $this->{Inflector::classify($this->name)}->data['Order']['id'] = $this->data['Order']['id'];
                $this->{Inflector::classify($this->name)}->data['Order']['order_status_id'] = 2; // completed
                $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                $this->Session->setFlash(__("Data Order Berhasil Diupdate."), 'default', array(), 'success');
                $this->redirect(array('action' => 'admin_cashier'));
            } else {
                $this->validationErrors = $this->{ Inflector::classify($this->name) }->validationErrors;
                $this->Session->setFlash(__("Harap mengecek kembali kesalahan dibawah."), 'default', array(), 'danger');
            }
        }
    }

    function admin_get_data_order_by_table($table_id = null) {
        $this->autoRender = false;
        if ($this->request->is("GET")) {
            if (!empty($table_id)) {
                $dataOrder = $this->Order->find("first", [
                    "conditions" => [
                        "Order.table_id" => $table_id,
                        "Order.order_status_id" => 1
                    ],
                    "order" => "Order.created DESC",
                    "contain" => [
                        "OrderDetail" => [
                            "RestoMenu"
                        ],
                        "Account" => [
                            "Biodata"
                        ],
                        "Table"
                    ]
                ]);
                if (!empty($dataOrder)) {
                    return json_encode($this->_generateStatusCode(206, "OK", $dataOrder));
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            } else {
                return json_encode($this->_generateStatusCode(401));
            }
        } else {
            return json_encode($this->_generateStatusCode(405, "Invalid Request type."));
        }
    }

    function admin_print_receipt() {
        $this->autoRender = false;
        if ($this->request->is("POST")) {
            $order_id = $this->request->data['order_id'];
            if (!empty($order_id)) {
                $dataOrder = $this->Order->find("first", [
                    "conditions" => [
                        "Order.id" => $order_id
                    ],
                    "contain" => [
                        "OrderDetail" => [
                            "RestoMenu"
                        ],
                        "Table"
                    ],
                ]);
                if (!empty($dataOrder)) {
                    $dataPrinter = ClassRegistry::init("EntityConfiguration")->find("first");
                    $ip_address_printer = $dataPrinter['EntityConfiguration']['ip_address_printer'];
                    $cashier = $this->Session->read("credential.admin.Biodata.full_name");
                    $dt = date("Y-m-d H:i:s");
                    $table = $dataOrder['Table']['name'];
                    if ($this->print_receipt($ip_address_printer, $dataOrder['OrderDetail'], $dt, $cashier, $table)) {
                        return json_encode($this->_generateStatusCode(206, "Now Printing..."));
                    } else {
                        return json_encode($this->_generateStatusCode(405, "Error : Cek Kembali Konfigurasi Printer Anda."));
                    }
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            } else {
                return json_encode($this->_generateStatusCode(405, "Invalid 'order_id' value."));
            }
        } else {
            return json_encode($this->_generateStatusCode(405, "Invalid Request Tyoe."));
        }
    }

    function print_order($ip_address_printer, $table, $dataOrderDetail, $datetime, $operator) {
        $this->autoRender = false;
        if (!empty($ip_address_printer) && !empty($dataOrderDetail) && !empty($table)) {
            App::import("Vendor", "escpos-php/autoload");
            App::import("Vendor", "PrintConnectors", array('file' => 'escpos-php/src/Mike42/Escpos/PrintConnectors/NetworkPrintConnector.php'));
            App::import("Vendor", "Escpos", array('file' => 'escpos-php/src/Mike42/Escpos/Printer.php'));
            App::import("Vendor", "Escpos", array('file' => 'escpos-php/src/Mike42/Escpos/EscposImage.php'));

            try {
                $connector = new Mike42\Escpos\PrintConnectors\NetworkPrintConnector($ip_address_printer);
                $printer = new Mike42\Escpos\Printer($connector);

                $items = [];
                $grandTotal = 0;
                $view = new View($this);
                $helper = $view->loadHelper("Html");
                foreach ($dataOrderDetail as $orderDetail) {
                    $items[] = new DataOrder($orderDetail['RestoMenu']['name'], $orderDetail['quantity'], $orderDetail['note']);
                    $grandTotal += $orderDetail['total'];
                }
                $total = new DataOrder("Total", $helper->separator_idr($grandTotal));

                $dataSistemConfig = ClassRegistry::init("EntityConfiguration")->find("first");
                $corporate_name = $dataSistemConfig['EntityConfiguration']['name'];
                $corporate_address = $dataSistemConfig['EntityConfiguration']['address'];

                // APP CORP NAME
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer->setEmphasis(true);
                $printer->text($corporate_name . "\n");
                $printer->selectPrintMode();
                $printer->text($corporate_address . "\n");
                $printer->feed();

                /* Title of receipt */
                $printer->setEmphasis(true);
                $printer->text("ORDER RECEIPT\n");
                $printer->setEmphasis(false);
                $printer->feed();

                // Date
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Tanggal  : " . $helper->cvtWaktuFull($datetime));
                $printer->feed();

                // Operator
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Operator : " . $operator);
                $printer->feed();

                /* Table Info */
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Meja     : " . $table);
                $printer->feed();

                /* Items */
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(true);
                $printer->text(new DataOrder('', 'QTY'));
                $printer->setEmphasis(false);
                foreach ($items as $item) {
                    $printer->text($item);
                }
                $printer->setEmphasis(true);
                $printer->feed();

                /* Cut the receipt and open the cash drawer */
                $printer->cut();

                $printer->close();

                return true;
            } catch (Exception $ex) {
                debug($ex);
                echo $ex;
                return false;
            }
        } else {
            return false;
        }
    }

    private function print_receipt($ip_address_printer, $dataOrderDetail, $datetime, $cashier, $table) {
        $this->autoRender = false;
        if (!empty($ip_address_printer) && !empty($dataOrderDetail)) {
            App::import("Vendor", "escpos-php/autoload");
            App::import("Vendor", "PrintConnectors", array('file' => 'escpos-php/src/Mike42/Escpos/PrintConnectors/NetworkPrintConnector.php'));
            App::import("Vendor", "Escpos", array('file' => 'escpos-php/src/Mike42/Escpos/Printer.php'));
            App::import("Vendor", "Escpos", array('file' => 'escpos-php/src/Mike42/Escpos/EscposImage.php'));

            try {
                $connector = new Mike42\Escpos\PrintConnectors\NetworkPrintConnector($ip_address_printer);
                $printer = new Mike42\Escpos\Printer($connector);

                $items = [];
                $grand_total = 0;
                $view = new View($this);
                $helper = $view->loadHelper("Html");
                foreach ($dataOrderDetail as $detail) {
                    $items[] = new Receipt($detail['RestoMenu']['name'], $detail['quantity'], $detail['amount'], $helper->separator_idr($detail['total']));
                    $grand_total += $detail['total'];
                }
                $total = new Receipt("Total", "", "", $helper->separator_idr($grand_total), true, false);

                $dataSistemConfig = ClassRegistry::init("EntityConfiguration")->find("first");
                $corporate_name = $dataSistemConfig['EntityConfiguration']['name'];
                $corporate_address = $dataSistemConfig['EntityConfiguration']['address'];

                // APP CORP NAME
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer->text($corporate_name . "\n");
                $printer->selectPrintMode();
                $printer->text($corporate_address . "\n");
                $printer->feed();

                /* Title of receipt */
                $printer->setEmphasis(true);
                $printer->text("INVOICE");
                $printer->feed(2);
                $printer->setEmphasis(false);

                // Date
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Tanggal  : " . $helper->cvtWaktuFull($datetime));
                $printer->feed();

                // Operator
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Cashier  : " . $cashier);
                $printer->feed();

                /* Table Info */
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Meja     : " . $table);
                $printer->feed(2);

                $printer->text("------------------------------------------------");
                $printer->feed();
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Description                            Sub Total");
                $printer->feed();
                $printer->text("------------------------------------------------");
                $printer->feed();

                /* Items */
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(false);
                foreach ($items as $item) {
                    $printer->text($item);
                }
                $printer->text("------------------------------------------------");
                $printer->feed();

                // Total
                $printer->setEmphasis(true);
                $printer->text($total);
                $printer->selectPrintMode();

                /* Footer */
                $printer->feed(2);
                $printer->text("------------------------------------------------");
                $printer->feed();
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer->text("Terima Kasih Atas Kunjungan Anda");
                $printer->feed(2);

                /* Cut the receipt and open the cash drawer */
                $printer->cut();

                $printer->close();

                return true;
            } catch (Exception $ex) {
                debug($ex);
                echo $ex;
                return false;
            }
        } else {
            return false;
        }
    }

}

class DataOrder {

    private $name;
    private $quantity;
    private $note;
    private $currencySign;

    public function __construct($name = '', $quantity = '', $note = '', $currencySign = false) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->currencySign = $currencySign;
        $this->note = $note;
    }

    public function __toString() {
        $rightCols = 10;
        $leftCols = 38;
        if ($this->currencySign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->currencySign ? 'IDR ' : '');
        $right = str_pad($sign . $this->quantity, $rightCols, ' ', STR_PAD_LEFT);
        $note_area = !empty($this->note) ? sprintf('(%5s)', $this->note) : "";
        return "$left$right\n     $note_area\n";
    }

}

class Receipt {

    private $menu_name;
    private $quantity;
    private $amount;
    private $total;
    private $type = "PCS";
    private $flag;
    private $currencySign;

    public function __construct($menu_name = "", $quantity = "", $amount = "", $total = "", $flag = false, $currencySign = false) {
        $this->menu_name = $menu_name;
        $this->quantity = $quantity;
        $this->amount = $amount;
        $this->total = $total;
        $this->flag = $flag;
        $this->currencySign = $currencySign;
    }

    public function __toString() {
        $rightCols = 10;
        $leftCols = 38;
        if ($this->currencySign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->menu_name, $leftCols);

        $sign = ($this->currencySign ? 'IDR ' : '');
        $right = str_pad($sign . $this->total, $rightCols, ' ', STR_PAD_LEFT);
        if (!$this->flag) {
            $area_detail = $this->give_spaces() . $this->quantity . " " . $this->type . $this->give_spaces() . $this->separator_idr($this->amount);
            return "$left$right\n$area_detail\n";
        } else {
            return "$left$right\n";
        }
    }

    private function give_spaces($n = 10) {
        $result = "";
        for ($i = 0; $i < $n; $i++) {
            $result .= " ";
        }
        return $result;
    }

    private function separator_idr($Rp) {
        if ($Rp == "") {
            return "0";
        }
        return number_format($Rp, 0, "", ".");
    }

}
