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
        "Device"
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
        $this->set("devices", ClassRegistry::init("Device")->find("list", ['fields' => ["Device.id", "Device.full_label"]]));
    }

    function api_post_order() {
        $this->autoRender = false;
        //$data = json_decode(file_get_contents('php://input'), true);
        if ($this->request->is("POST")) {
            $data = json_decode($this->data['order'], true);
            if (!empty($data)) {
                // check if the device is registered by the system
                $ipv4_device = $data['ipv4'];
                $dataDevice = ClassRegistry::init("Device")->findByIpAddress($ipv4_device);
                if (!empty($dataDevice)) {
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
                                "device_id" => $dataDevice['Device']['id']
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
                    return json_encode($this->_generateStatusCode(401, "Device Not Registered."));
                }
            } else {
                return json_encode($this->_generateStatusCode(401, "failed"));
            }
        } else {
            return json_encode($this->_generateStatusCode(405, "Invalid Request Type."));
        }
    }

    function api_get_order() {
        $this->autoRender = false;
    }

}
