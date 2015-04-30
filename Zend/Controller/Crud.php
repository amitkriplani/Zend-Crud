<?php

class Zend_Controller_Crud extends Zend_Controller_Action {

    protected $_model;

    public function init() {
        parent::init();
        $this->_helper->ViewRenderer->setNoController(1)->setRender('crud/' . $this->getRequest()->getActionName());
        $this->view->controller = $this->getRequest()->getControllerName();
        $this->view->action = $this->getRequest()->getActionName();
    }

    /**
     * 
     * @return Zend_DataObject_Abstract
     */
    protected function getModel() {
        if (is_string($this->_model)) {
            $class = 'Zend_DataObject_' . $this->_model;
            $this->_model = new $class;
        }
        return $this->_model;
    }

    public function addAction() {
        $form = new Zend_MagicForm();
        $form->constructFromDbTable($this->getModel(), $this->_website);
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $status = $this->getModel()->insert($form->getValues());
            if ($status == 'OK') {
                $this->redirectToIndex();
            } else {
                $form->setErrors(array($status));
            }
        }
        $this->view->form = $form;
        $this->_helper->ViewRenderer->setRender('crud/edit');
    }

    public function copyAction() {
        $data = $this->getModel()->find($this->getRequest()->getParam('id'));
        if (!$data) {
            throw new Zend_Controller_Action_Exception('data not found');
        }
        $form = new Zend_MagicForm();
        $form->constructFromDbTable($this->getModel(), $this->_website);
        $form->setDefaults($data);
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $status = $this->getModel()->insert($form->getValues());
            if ($status == 'OK') {
                $this->redirectToIndex();
            } else {
                $form->setErrors(array($status));
            }
        }
        $this->view->form = $form;
        $this->_helper->ViewRenderer->setRender('crud/edit');
    }

    public function indexAction() {
        $this->view->pk = $this->getModel()->getPk();
        $this->view->data = $this->getModel()->fetchAll(array('website = ?' => $this->_website['host']));
        $this->view->action = 'list';
    }

    public function viewAction() {
        $data = $this->getModel()->find($this->getRequest()->getParam('id'));
        if (!$data) {
            throw new Zend_Controller_Action_Exception('data not found');
        }
        $this->view->pk = $this->getModel()->getPk();
        $this->view->data = $data;
    }

    public function deleteAction() {
        $this->getModel()->delete($this->getRequest()->getParam('id'));
        $this->redirectToIndex();
    }

    public function editAction() {
        $data = $this->getModel()->find($this->getRequest()->getParam('id'));
        if (!$data) {
            throw new Zend_Controller_Action_Exception('data not found');
        }
        $form = new Zend_MagicForm();
        $form->constructFromDbTable($this->getModel(), $this->_website);
        $form->setDefaults($data);
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $status = $this->getModel()->update($this->getRequest()->getParam('id'), $form->getValues());
            if ($status == 'OK') {
                $this->redirectToIndex();
            } else {
                $form->setErrors(array($status));
            }
        }
        $this->view->form = $form;
    }

    protected function redirectToIndex() {
        $this->redirect(
                '/' . $this->getRequest()->getModuleName() .
                '/' . $this->getRequest()->getControllerName()
        );
    }

}
