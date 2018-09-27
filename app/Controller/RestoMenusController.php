<?php

App::uses('AppController', 'Controller');

class RestoMenusController extends AppController {

    var $name = "RestoMenus";
    var $disabledAction = array(
    );
    var $contain = array(
        "MenuCategory"
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
        $this->set("menuCategories", ClassRegistry::init("MenuCategory")->list_category());
    }

    function admin_add() {
        if ($this->request->is("post")) {
            $this->{ Inflector::classify($this->name) }->set($this->data);
            if ($this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('validate' => 'only', "deep" => true))) {
                $this->RestoMenu->_numberSeperatorRemover();
                if (!empty($this->RestoMenu->data['RestoMenu']['gambar']['name'])) {
                    App::import("Vendor", "qqUploader");
                    $allowedExt = array("jpg", "jpeg", "png");
                    $size = 10 * 1024 * 1024;
                    $uploader = new qqFileUploader($allowedExt, $size, $this->RestoMenu->data['RestoMenu']['gambar']);
                    $result = $uploader->handleUpload("img" . DS . "menu" . DS);
                    switch ($result['status']) {
                        case 206:
                            $this->RestoMenu->data['RestoMenu']['image_path'] = "/" . $result['data']['folder'] . $result['data']['fileName'];
                            break;
                    }
                    unset($this->RestoMenu->data['RestoMenu']['gambar']);
                }
                $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                $this->Session->setFlash(__("Data berhasil disimpan"), 'default', array(), 'success');
                $this->redirect(array('action' => 'admin_index'));
            } else {
                $this->validationErrors = $this->{ Inflector::classify($this->name) }->validationErrors;
                $this->Session->setFlash(__("Harap mengecek kembali kesalahan dibawah."), 'default', array(), 'danger');
            }
        }
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
                        $this->RestoMenu->_numberSeperatorRemover();
                        if (!empty($this->RestoMenu->data['RestoMenu']['gambar']['name'])) {
                            App::import("Vendor", "qqUploader");
                            $allowedExt = array("jpg", "jpeg", "png");
                            $size = 10 * 1024 * 1024;
                            $uploader = new qqFileUploader($allowedExt, $size, $this->RestoMenu->data['RestoMenu']['gambar']);
                            $result = $uploader->handleUpload("img" . DS . "menu" . DS);
                            switch ($result['status']) {
                                case 206:
                                    $this->RestoMenu->data['RestoMenu']['image_path'] = "/" . $result['data']['folder'] . $result['data']['fileName'];
                                    break;
                            }
                            unset($this->RestoMenu->data['RestoMenu']['gambar']);
                        }
                        $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                        $this->Session->setFlash(__("Data berhasil diubah"), 'default', array(), 'success');
                        $this->redirect(array('action' => 'admin_index'));
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

    public function api_get_menu_list() {
        $this->autoRender = false;
        if($this->request->is("GET")) {            
            $conds = [];
            if(isset($this->request->query['category_id']) && !empty($this->request->query['category_id'])) {
                $menu_category_id = $this->request->query['category_id'];
                $conds = [
                    "RestoMenu.menu_category_id" => $menu_category_id
                ];
            }
            $data = $this->RestoMenu->find("all",[
                "conditions" => [
                    $conds
                ],
                "contain" => [
                    "MenuCategory"
                ],
                "order" => "RestoMenu.name"
            ]);
            if(!empty($data)) {
                return json_encode($this->_generateStatusCode(206, "Data Found.", $data));
            } else {
                return json_encode($this->_generateStatusCode(401));
            }
        } else {
            return json_encode($this->_generateStatusCode(400));
        }
    }
    
    public function api_get_menu() {
        $this->autoRender = false;
        if($this->request->is("GET")) {
            if(isset($this->request->query['menu_id']) && !empty($this->request->query['menu_id'])) {
                $menu_id = $this->request->query['menu_id'];
                $data = $this->RestoMenu->find("first",[
                    "conditions" => [
                        "RestoMenu.id" => $menu_id
                    ],
                    "recursive" => -1
                ]);
                if(!empty($data)) {
                    return json_encode($this->_generateStatusCode(206, "Data Found.", $data));
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            } else {
                return json_encode($this->_generateStatusCode(404, "Either Empty or Invalid Params."));
            }
        } else {
            return json_encode($this->_generateStatusCode(400, "Invalid Request Type."));
        }
    }
    
    public function api_get_menu_by_name() {
        $this->autoRender = false;
        if($this->request->is("GET")) {
            $menu_name = $this->request->query['menu_name'];
            if(!empty($menu_name)) {
                $menu_name = str_replace("-", " ", $menu_name);
                $data = $this->RestoMenu->find("first",[
                    "conditions" => [
                        "RestoMenu.name" => $menu_name
                    ],
                    "recursive" => -1
                ]);
                if(!empty($data)) {
                    return json_encode($this->_generateStatusCode(206, "OK", $data));
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            } else {
                return json_encode($this->_generateStatusCode(404, "Menu name not found."));
            }
        } else {
            return json_encode($this->_generateStatusCode(400, "Invalid Request Type."));
        }
    }
    
    public function admin_get_data_resto_menu($resto_menu_id = null) {
        $this->autoRender = false;
        if(!empty($resto_menu_id)) {
            if($this->request->is("GET")) {
                $data = $this->RestoMenu->find("first",[
                    "conditions" => [
                        "RestoMenu.id" => $resto_menu_id
                    ],
                    "recursive" => -1
                ]);
                if(!empty($data)) {
                    return json_encode($this->_generateStatusCode(206, "OK", $data));
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            } else {
                return json_encode($this->_generateStatusCode(405, "invalid request type."));
            }
        } else {
            return json_encode($this->_generateStatusCode(401, "ID not found."));
        }
    }
}
