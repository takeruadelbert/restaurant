<?php

App::uses('AppController', 'Controller');

class TablesController extends AppController {

    var $name = "Tables";
    var $disabledAction = array(
    );
    var $contain = array(
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->_setPageInfo("admin_index", "");
        $this->_setPageInfo("admin_add", "");
        $this->_setPageInfo("admin_edit", "");
    }

    function api_get_table_list() {
        $this->autoRender = false;
        if ($this->request->is("GET")) {
            $data = $this->Table->find("list", [
                "fields" => [
                    "Table.id",
                    "Table.name"
                ],
                "recursive" => -1,
                'order' => "Table.name"
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
            $pos_x = $this->{Inflector::classify($this->name)}->data['Table']['pos_x'];
            $pos_y = $this->{Inflector::classify($this->name)}->data['Table']['pos_y'];
            if ($this->Table->check_position($pos_x, $pos_y)) {
                if ($this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('validate' => 'only', "deep" => true))) {
                    $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                    $this->Session->setFlash(__("Data berhasil disimpan"), 'default', array(), 'success');
                    $this->redirect(array('action' => 'admin_index'));
                } else {
                    $this->validationErrors = $this->{ Inflector::classify($this->name) }->validationErrors;
                    $this->Session->setFlash(__("Harap mengecek kembali kesalahan dibawah."), 'default', array(), 'danger');
                }
            } else {
                $this->Session->setFlash(__("Koordinat Posisi Meja (x,y) Tidak Boleh Ada Yang Sama."), 'default', array(), 'danger');
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
                $is_valid = true;
                if ($this->data['Table']['is_pos_change']) {
                    $pos_x = $this->{Inflector::classify($this->name)}->data['Table']['pos_x'];
                    $pos_y = $this->{Inflector::classify($this->name)}->data['Table']['pos_y'];
                    if ($this->Table->check_position($pos_x, $pos_y)) {
                        $is_valid = true;
                    } else {
                        $is_valid = false;
                        $this->Session->setFlash(__("Koordinat Posisi Meja (x,y) Tidak Boleh Ada Yang Sama."), 'default', array(), 'danger');
                        $this->redirect($this->referer());
                    }
                }
                if ($is_valid) {
                    if ($this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('validate' => 'only', "deep" => true))) {
                        if (!is_null($id)) {
                            $this->{ Inflector::classify($this->name) }->saveAll($this->{ Inflector::classify($this->name) }->data, array('deep' => true));
                            $this->Session->setFlash(__("Data berhasil diubah"), 'default', array(), 'success');
                            $this->redirect(array('action' => 'admin_index'));
                        }
                    } else {
                        $this->request->data[Inflector::classify($this->name)]["id"] = $id;
                        $this->validationErrors = $this->{ Inflector::classify($this->name) }->validationErrors;
                    }
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

    function admin_3DRender() {
        $dataTable = $this->Table->find("all", ['recursive' => -1]);
        $result = [];
        if (!empty($dataTable)) {
            foreach ($dataTable as $table) {
                $result[] = $table['Table']['name'];
                $result[] = "";
                $result[] = "";
                $result[] = $table['Table']['pos_x'];
                $result[] = $table['Table']['pos_y'];
            }
        }
        $encoded_result = json_encode($result);
        $this->set(compact('encoded_result'));
        $this->layout = _TEMPLATE_DIR . "/{$this->template}/3DRender";
    }

}
