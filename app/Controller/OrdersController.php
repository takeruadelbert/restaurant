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
        ]
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
                if(!empty($dataOrder)) {
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

}
