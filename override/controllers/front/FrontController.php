<?php

class FrontController extends FrontControllerCore
{
    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(array(
            _THEME_JS_DIR_.'tools.js',  // retro compat themes 1.5
        ));
    }
}