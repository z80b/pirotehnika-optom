<?php

class YamoduleOverride extends Yamodule
{
    public function install() {
        if (!parent::install() || !$this->registerHook('Header')) {
            return false;
        }
        return true;
    }
    public function hookHeader()
    {
        $data = '';
        if (!Configuration::get('YA_METRIKA_ACTIVE')) {
            $data .= 'var celi_order = false;';
            $data .= 'var celi_cart = false;';
            $data .= 'var celi_wishlist = false;';
            return '<script type="text/javascript">'.$data.'</script>';
        }

        if (Configuration::get('YA_METRIKA_CELI_ORDER')) {
            $data .= 'var celi_order = true;';
        } else {
            $data .= 'var celi_order = false;';
        }

        if (Configuration::get('YA_METRIKA_CELI_CART')) {
            $data .= 'var celi_cart = true;';
        } else {
            $data .= 'var celi_cart = false;';
        }

        if (Configuration::get('YA_METRIKA_CELI_WISHLIST')) {
            $data .= 'var celi_wishlist = true;';
        } else {
            $data .= 'var celi_wishlist = false;';
        }

        if (Configuration::get('YA_METRIKA_CODE') != '') {
            return '<script type="text/javascript">'.$data.Configuration::get('YA_METRIKA_CODE').'</script>';
        }
    }

}
