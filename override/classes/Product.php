<?php


class Product extends ProductCore
{
    function getProductCartInfo(){
        $id = $this->id;
        return self::getProductCartInfoStatic($id);
    }

    static function getProductCartInfoStatic($id=null){
        if(!$id) return array();
        $context = Context::getContext();
        $cart = $context->cart;

        $info = array();
        foreach($cart->getProducts() as $cartProd){
            if($cartProd['id_product'] == $id) $info = $cartProd;
        }
        return $info;
    }

    static function SGetProductUnity($unity){
        $res = '';
        switch ($unity) {
            case 1:
                $res = 'уп.';
                break;
            case 2:
                $res = 'бл.';
                break;
            default:
                $res = 'шт.';
        }
        return $res;
    }

    static function instance($id, $full=true){
        return new Product($id, $full);
    }

}