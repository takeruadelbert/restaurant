<?php

App::uses('AppController', 'Controller');

class OrdersController extends AppController {

    var $name = "Orders";
    var $disabledAction = array(
    );
    var $contain = array(
        "Table",
        "OrderDetail" => [
            "RestoMenu"
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
                                    "resto_menu_id" => $detail['resto_menu_id'],
                                    "quantity" => $detail['quantity'],
                                    "amount" => $detail['amount'],
                                    "note" => @$detail['note']
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
                        try {
                            $this->Order->saveAll($result);
                            return json_encode($this->_generateStatusCode(200, "Data has been saved successfully."));
                        } catch (Exception $ex) {
                            debug("Error : failed to save.");
                            return json_encode($this->_generateStatusCode(405, "Error : Failed to save."));
                        }
                    } else {
                        return json_encode($this->_generateStatusCode(401, "No Table found."));
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
    
    function admin_cashier() {
        if($this->request->is("POST")) {
            
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

    function print_order($ip_address_printer, $table, $dataOrderDetail) {
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
                    $items[] = new DataOrder($orderDetail['RestoMenu']['name'], $orderDetail['quantity']);
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
                
                /* Table Info */
                $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_LEFT);
                $printer->text("Meja : " . $table);
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

}

class DataOrder {
    private $name;
    private $quantity;
    private $currencySign;
    
    public function __construct($name = '', $quantity = '', $currencySign = false) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->currencySign = $currencySign;
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
        return "$left$right\n";
    }

}
