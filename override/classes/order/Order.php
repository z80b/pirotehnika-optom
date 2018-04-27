<?php

class Order extends OrderCore {
    public $full_paid;
    public $full_paid_date;
    public $full_paid_reason;

    public function __construct($id = null, $id_lang = null) {
        self::$definition['fields']['full_paid'] = array('type' => self::TYPE_BOOL);
        self::$definition['fields']['full_paid_date'] = array('type' => self::TYPE_DATE);
        self::$definition['fields']['full_paid_reason'] = array('type' => self::TYPE_STRING);
        parent::__construct($id, $id_lang);
    }
}