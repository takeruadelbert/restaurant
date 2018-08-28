<?php

App::uses('AppController', 'Controller');

class MenuCategoriesController extends AppController {

    var $name = "MenuCategories";
    var $disabledAction = array(
    );
    var $contain = array(
        "Parent"
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
        $this->set("parents", ClassRegistry::init("MenuCategory")->list_category());
    }

    function api_get_category_list() {
        $this->autoRender = false;
        if ($this->request->is("GET")) {
            $data = $this->MenuCategory->find("list", [
                "fields" => [
                    "MenuCategory.id",
                    "MenuCategory.name"
                ],
                "recursive" => -1,
                "order" => "MenuCategory.name"
            ]);
            if (!empty($data)) {
                return json_encode($this->_generateStatusCode(205, "Data Found.", $data));
            } else {
                return json_encode($this->_generateStatusCode(401));
            }
        } else {
            return json_encode($this->_generateStatusCode(400));
        }
    }

    function admin_add() {
        if ($this->request->is("post")) {
            $this->{ Inflector::classify($this->name) }->set($this->data);
            if ($this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('validate' => 'only', "deep" => true))) {
                if (!empty($this->MenuCategory->data['MenuCategory']['gambar']['name'])) {
                    App::import("Vendor", "qqUploader");
                    $allowedExt = array("jpg", "jpeg", "png");
                    $size = 10 * 1024 * 1024;
                    $uploader = new qqFileUploader($allowedExt, $size, $this->MenuCategory->data['MenuCategory']['gambar']);
                    $result = $uploader->handleUpload("img" . DS . "category" . DS);
                    switch ($result['status']) {
                        case 206:
                            $this->MenuCategory->data['MenuCategory']['image_path'] = "/" . $result['data']['folder'] . $result['data']['fileName'];
                            break;
                    }
                    unset($this->MenuCategory->data['MenuCategory']['gambar']);
                    $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                    $this->Session->setFlash(__("Data berhasil disimpan"), 'default', array(), 'success');
                    $this->redirect(array('action' => 'admin_index'));
                } else {
                    $this->Session->setFlash(__("Preview Image Wajib Diupload."), 'default', array(), 'danger');
                }
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
                        if (!empty($this->MenuCategory->data['MenuCategory']['gambar']['name'])) {
                            App::import("Vendor", "qqUploader");
                            $allowedExt = array("jpg", "jpeg", "png");
                            $size = 10 * 1024 * 1024;
                            $uploader = new qqFileUploader($allowedExt, $size, $this->MenuCategory->data['MenuCategory']['gambar']);
                            $result = $uploader->handleUpload("img" . DS . "category" . DS);
                            switch ($result['status']) {
                                case 206:
                                    $this->MenuCategory->data['MenuCategory']['image_path'] = "/" . $result['data']['folder'] . $result['data']['fileName'];
                                    break;
                            }
                            unset($this->MenuCategory->data['MenuCategory']['gambar']);
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

    function api_get_category($category_id = null) {
        $this->autoRender = false;
        if ($this->request->is("GET")) {
            if (!empty($category_id)) {
                $data = $this->MenuCategory->find("first", [
                    "conditions" => [
                        "MenuCategory.id" => $category_id
                    ],
                    "recursive" => -1
                ]);
                if (!empty($data)) {
                    return json_encode($this->_generateStatusCode(206, "Data Foubd.", $data));
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            } else {
                $data = $this->MenuCategory->find("all", [
                    "conditions" => [
                        "MenuCategory.parent_id !=" => null
                    ],
                    "recursive" => -1,
                    "order" => "MenuCategory.ordering_number"
                ]);
                if (!empty($data)) {
                    return json_encode($this->_generateStatusCode(206, "Data Found.", $data));
                } else {
                    return json_encode($this->_generateStatusCode(401));
                }
            }
        } else {
            return json_encode($this->_generateStatusCode(400, "Invalid Request."));
        }
    }

}
