<?php
class ContactController extends ContactControllerCore {
  /*
    * module: eicaptcha
    * date: 2018-03-23 18:06:36
    * version: 0.4.14
    */
    public function checkAccess() {
    return (bool)Hook::exec('contactFormAccess');
  }
  /*
    * module: eicaptcha
    * date: 2018-03-23 18:06:36
    * version: 0.4.14
    */
    public function initCursedPage() {
    parent::setMedia();
    if (!empty($this->redirect_after)) {
      parent::redirect();
    }
    if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
      parent::initHeader();
    }
    parent::initContent();
    if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className))) {
      parent::initFooter();
    }
    parent::display();
    die;
  }
}
