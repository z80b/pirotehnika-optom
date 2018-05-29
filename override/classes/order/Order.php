<?php

class OrderCore extends ObjectModel {
	public $full_paid;
	public $full_paid_date;
	public $full_paid_reason;

	public function getFields() {
		$fields = parent::getFields();

		$fields['fields']['full_paid'] = array('type' => self::TYPE_BOOL);
		$fields['fields']['full_paid_date'] = array('type' => self::TYPE_DATE);
		$fields['fields']['full_paid_reason'] = array('type' => self::TYPE_STRING);
	}
}