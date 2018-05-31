<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Manufacturer $object
 */
class AdminMy_PaymentsControllerCore extends AdminController
{
    public $bootstrap = true ;
    public $my_tip_amount = 1;
    /** @var array countries list */
    protected $mpt_array = array();
    protected $mpv_array = array();
    protected $mpvp_array = array();
    protected $mpvr_array = array();
    protected $mps_array = array();
    protected $mpk_array = array();
	protected $my_paym_filt = 1;
	protected $mpvids = array();
	protected $mpsposs = array();
	protected $mpkas = array();

    public function __construct()
    {
        parent::__construct();
        $this->table = 'my_payments';
        $this->className = 'My_Payments';
        $this->lang = false;
        $this->deleted = false;
        $this->allow_export = false;
        $this->list_id = 'my_payments';
        $this->identifier = 'id_my_payments';
        $this->_defaultOrderBy = 'date_add';


        $this->context = Context::getContext();
//		$this->my_paym_filt = 0;
		$this->_select = '
		a.id_my_payments,
		a.date_payment,
		a.rperiod,
		mpt.`name` AS `mptname`,
		mpv.`name` AS `mpvname`,
		mps.`name` AS `mpsname`,
		mpk.`name` AS `mpkname`,
		c.`my_name` AS `customer`,
		a.`summa_all` AS `summa`,
		a.`summa_yes` AS `summa_yes`,
		a.`summa_no` AS `summa_no`,
		a.`status`,
		a.`prim`,
		a.`date_add`,
		IF(a.`id_my_payments_tip` = 1, 1, 0) badge_success,
		IF(a.`summa_all` = 0, 1, 0) badge_danger,
		IF(a.`summa_no` > 0, 1, 0) badge_warning';

        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'my_payments_tip` mpt ON (mpt.`id_my_payments_tip` = a.`id_my_payments_tip`)
		LEFT JOIN `'._DB_PREFIX_.'my_payments_vid` mpv ON (mpv.`id_my_payments_vid` = a.`id_my_payments_vid`)
		LEFT JOIN `'._DB_PREFIX_.'my_payments_spos` mps ON (mps.`id_my_payments_spos` = a.`id_my_payments_spos`)
		LEFT JOIN `'._DB_PREFIX_.'my_payments_kas` mpk ON (mpk.`id_my_payments_kas` = a.`id_my_payments_kas`)
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_orderBy = 'id_my_payments';
        $this->_orderWay = 'DESC';
		if ($this->my_paym_filt == 0) {
			$this->_where = 'AND a.`id_shop` = '.(int)$this->context->shop->id;
		} else {
			$this->_where = 'AND a.`id_shop` = '.(int)$this->context->shop->id.' AND a.`id_my_payments_tip` = 1';
		}
        $this->_use_found_rows = true;

//        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $mpts = my_payments::getMy_Payments_Tips($this->my_paym_filt);
        foreach ($mpts as $mpt) {
           $this->mpt_array[$mpt['id_my_payments_tip']] = $mpt['name'];
        }
		// Все виды прихода и расхода
        $this->mpvids = my_payments::getMy_Payments_Vids();
        foreach ($this->mpvids as $mpt) {
           $this->mpv_array[$mpt['id_my_payments_vid']] = $mpt['name'];
        }
/* 		// Виды прихода
        $mptk = my_payments::getMy_Payments_Vids();
        foreach ($mptk as $mpt) {
           $this->mpvp_array[$mpt['id_my_payments_vid']] = $mpt['name'];
        }
		// Виды расхода
        $mptk = my_payments::getMy_Payments_Vids();
        foreach ($mptk as $mpt) {
           $this->mpvr_array[$mpt['id_my_payments_vid']] = $mpt['name'];
        }
 */
        $this->mpsposs = my_payments::getMy_Payments_sposs();
        foreach ($this->mpsposs as $mpt) {
           $this->mps_array[$mpt['id_my_payments_spos']] = $mpt['name'];
        }

        $this->mpkass = my_payments::getMy_Payments_kass();
        foreach ($this->mpkas as $mpt) {
           $this->mpk_array[$mpt['id_my_payments_kas']] = $mpt['name'];
        }

		
        $this->fields_list = array(
            'mpsname' => array(
                'title' => $this->l('Способ'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->mps_array, // массив после sql
                'filter_key' => 'mps!id_my_payments_spos',
                'filter_type' => 'int',
                'order_key' => 'mpsname' // алиас из sql
            ),
/*             'mpkname' => array(
                'title' => $this->l('Касса'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->mpk_array, // массив после sql
                'filter_key' => 'mpk!id_my_payments_kas',
                'filter_type' => 'int',
                'order_key' => 'mpkname' // алиас из sql
            ),
 */
            'customer' => array(
                'title' => $this->l('Customer'), // ???
                'havingFilter' => true,
            ),
            'summa' => array(
                'title' => $this->l('Сумма'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'badge_danger' => true
            ),
            'summa_no' => array(
                'title' => $this->l('К распределению'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'badge_warning' => true
            ),
            'prim' => array(
                'title' => $this->l('Description'),
                'width' => 'auto'
            ),
            'date_payment' => array(
                'title' => $this->l('Date'),
                'align' => 'text-right',
                'type' => 'date',
                'filter_key' => 'a!date_payment'
            ),
        );
        if ($this->my_paym_filt == 0) {
            $this->fields_list = array_merge(array(
				'id_my_payments' => array(
					'title' => $this->l('ID'),
					'align' => 'center',
					'class' => 'fixed-width-xs'
				),
				'mptname' => array(
					'title' => $this->l('Тип'),
					'type' => 'select',
					'color' => 'color',
					'list' => $this->mpt_array, // массив после sql
					'filter_key' => 'mpt!id_my_payments_tip',
					'filter_type' => 'int',
					'order_key' => 'mptname', // алиас из sql
					'align' => 'left',
					'badge_success' => true

				),
				'mpvname' => array(
					'title' => $this->l('Вид'),
					'type' => 'select',
					'color' => 'color',
					'list' => $this->mpv_array, // массив после sql 
					'filter_key' => 'mpv!id_my_payments_vid',
					'filter_type' => 'int',
					'order_key' => 'mpvname' // алиас из sql
				),
            ),$this->fields_list );
        } else {
            $this->fields_list = array_merge(array(
				'id_my_payments' => array(
					'title' => $this->l('ID'),
					'align' => 'center',
					'class' => 'fixed-width-xs'
				),
            ),$this->fields_list );
		}

    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->addJqueryUI('ui.datepicker');
        $this->addJS(_PS_JS_DIR_.'vendor/d3.v3.min.js');
        $this->addJS('https://maps.googleapis.com/maps/api/js?v=3.exp');
//            $this->addJS(_PS_JS_DIR_.'admin/orders.js');
            $this->addJS(_PS_JS_DIR_.'tools.js');
            $this->addJqueryPlugin('autocomplete');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();  

		if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_my_payments'] = array(
                'href' => self::$currentIndex.'&addmy_payments&token='.$this->token,
                'desc' => $this->l('Add new incoming', null, null, false),
                'icon' => 'process-icon-new'
            );
			if ($this->my_paym_filt == 0) {
            $this->page_header_toolbar_btn['new_my_payments_expence'] = array(
                'href' => self::$currentIndex.'&addmy_payments&expense&token='.$this->token,
                'desc' => $this->l('Add new expense', null, null, false),
                'icon' => 'process-icon-new'
            );

			}
			
        } 
       if ($this->display == 'add') {
//            unset($this->page_header_toolbar_btn['save']);
        }

 
    }



    /**
     * Display editaddresses action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayEditaddressesLink($token = null, $id)
    {
        if (!array_key_exists('editaddresses', self::$cache_lang)) {
            self::$cache_lang['editaddresses'] = $this->l('Edit');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&editaddresses&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['editaddresses'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_edit.tpl');
    }
    protected function copyFromPost(&$object, $table)
    {
        parent::copyFromPost($object, $table);
// Иванов		
		$object->vid = Tools::getValue('vid');
	}

	public function ajaxProcessDeleteRaspPayment() {
		$id_del_payments = Tools::getValue('id_payment');
		Order::deleteOrderPayment($id_del_payments);
		$nomord = Tools::getValue('nomOrder');
		$tek_order = new Order($nomord);
		$pna = Tools::getValue('paymAmount');
		$tek_order->total_paid_real -= $pna;
		$tek_order->update();
		$idmp = Tools::getValue('id_my_payments');
		$summa_yes = my_payments::getTotalRaspred($idmp);
		$summa_no = Tools::getValue('summa_all') - $summa_yes;
		$this->ajaxProcessfindAllNotAmountOrders($idmp, Tools::getValue('tek_order'), $summa_yes, $summa_no);

	}
	
	// Сохраняет новую / или измененную оплату
	public function ajaxProcessSavePayment() {

		$id_new_payments = Tools::getValue('id_payment');
		if ($id_new_payments >0 ) {
			$my_paym = new My_Payments($id_new_payments);
			$my_paym->date_upd =  date('Y-m-d H:i:s');
			$isnew = false;
		} else {
			$my_paym = new $this->className();
			$my_paym->id = null;
			$my_paym->summa_yes = 0;
			$my_paym->summa_no = 0;
			$my_paym->rperiod = Configuration::get('PS_ORDER_RPERIOD', null, null, (int)$this->context->shop->id);
			$my_paym->date_add =  date('Y-m-d H:i:s');
			$my_paym->date_upd =  date('Y-m-d H:i:s');
			$my_paym->id_shop = (int)$this->context->shop->id;
			$isnew = true;
		}	
		$dataps = json_decode(Tools::getValue('dataps'), TRUE);
		$my_paym->date_payment =  Tools::getValue('date_payment');
		$my_paym->id_my_payments_spos = Tools::getValue('id_my_payments_spos');
        if (count($dataps)) {
			$my_payment_methods = array();
			foreach (PaymentModule::getInstalledPaymentModules() as $my_payment) {
				$my_module = Module::getInstanceByName($my_payment['name']);
				if (Validate::isLoadedObject($my_module) && $my_module->active) {
					$my_payment_methods[] = $my_module->displayName;
				}
			}
            foreach ($dataps as $datap) {
// *******************************************
 
                $mpamount = $datap[3];
				// Можно через функцию
//				$order = new Order(Order::getIdByReference($datap[2]));
				// А можно прямо из массива
				$order = new Order($datap[5]);
//$text66 = '  ajax  - '.'search yes - '.$nid.' - '.$order->id.'*' ;
//file_put_contents('somefile.txt', PHP_EOL.$text66, FILE_APPEND);

                if (!Validate::isLoadedObject($order)) {
                    $this->errors[] = Tools::displayError('The order cannot be found');
                } elseif (!Validate::isNegativePrice($mpamount) || !(float)$mpamount) {
                    $this->errors[] = Tools::displayError('The amount is invalid.');
                } else {
                    if (!$order->addOrderPayment($mpamount, $my_payment_methods[($my_paym->id_my_payments_spos - 1)], $datap[4], null, $my_paym->date_payment, null, $datap[0])) {
                        $this->errors[] = Tools::displayError('An error occurred during payment.');
                    } 
                }
// *******************************************
			}
        }
		$my_paym->summa_all = Tools::getValue('summa_all');
		if ($isnew) {
			$my_paym->summa_yes = 0;
		} else {
			$my_paym->summa_yes = my_payments::getTotalRaspred($id_new_payments);
		}	
		$my_paym->summa_no = $my_paym->summa_all - $my_paym->summa_yes;
//$text66 = '  ajax  - '.'search yes - '.$my_paym->summa_yes.' - '.$my_paym->summa_no.'*' ;
//file_put_contents('somefile.txt', PHP_EOL.$text66, FILE_APPEND);
		$my_paym->id_my_payments_tip = Tools::getValue('id_my_payments_tip');
		$my_paym->id_my_payments_vid = Tools::getValue('id_my_payments_vid');
		$my_paym->id_my_payments_kas = Tools::getValue('id_my_payments_kas');
		$my_paym->order = Tools::getValue('nomOrder');
		$neworder = $my_paym->order;
		$my_paym->id_customer = Tools::getValue('id_customer');
		$my_paym->prim = Tools::getValue('prim');
 
		if (($id_new_payments >0 )) {
			if (!$my_paym->update()) {
				$this->errors[] = Tools::displayError('An error occurred while adding new payment.');
			}
		} else {
 			if (!$my_paym->add()) {
				$this->errors[] = Tools::displayError('An error occurred while updating payment information.');
			}
		}	
		$newidpm = $my_paym->id;
		$this->ajaxProcessfindAllNotAmountOrders($newidpm, $neworder, $my_paym->summa_yes, $my_paym->summa_no);
	}
//*************************
	 public function ajaxProcessfindAllNotAmountOrders($payment_id = null, $ordn = null, $sum_yes = 0, $sum_no = 0)
    {
		if (is_null($payment_id)) {
			$payment_id = (int)Tools::getValue('id_payment');
		}
		if (is_null($ordn)) {$ordn = Tools::getValue('nomOrder');}
        $orders = Order::getNotAmountOrders(null,$ordn);  // неоплаченные заказы
        $payments = OrderPayment::getByMyPaymetsId($payment_id);  // разнесеные платежи по этой оплате

        if (count($orders)) {
            foreach ($orders as $order) {
                $order['total_paid'] = Tools::displayPrice($order['total_paid'], $currency);
                $order['total_paid_real'] = Tools::displayPrice($order['total_paid_real'], $currency);
                $order['credit'] = Tools::displayPrice($order['credit'], $currency);
            }
        }
        if ($orders || $payments) {
            $to_return = array('payments' => $payments,
                               'orders' => $orders,
                               'found' => true,
                               'sumyes' => $sum_yes,
                               'sumno' => $sum_no,
							   'newpid' => $payment_id);
        } else {
            $to_return =  array('found' => false,
                               'sumyes' => $sum_yes,
                               'sumno' => $sum_no,
								'newpid' => $payment_id);
        }
//$text66 = '  ajax  - '.'search yes'.'*' ;
//file_put_contents('somefile.txt', PHP_EOL.$text66, FILE_APPEND);

        echo Tools::jsonEncode($to_return);
    }

	
	
    public function renderForm()
    {
		parent::renderForm();
        unset($this->toolbar_btn['save']);
        $my_payment = new My_Payments(Tools::getValue('id_my_payments'));
		$this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));

        $defaults_order_state = array('cheque' => (int)Configuration::get('PS_OS_CHEQUE'),
                                                'bankwire' => (int)Configuration::get('PS_OS_BANKWIRE'),
                                                'cashondelivery' => Configuration::get('PS_OS_COD_VALIDATION') ? (int)Configuration::get('PS_OS_COD_VALIDATION') : (int)Configuration::get('PS_OS_PREPARATION'),
                                                'other' => (int)Configuration::get('PS_OS_PAYMENT'));
        $payment_modules = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $p_module) {
            $payment_modules[] = Module::getInstanceById((int)$p_module['id_module']);
        }
        $this->context->smarty->assign(array(
			'date_payment' => ($this->display == 'edit' ? $my_payment->date_payment : date("Y-m-d")),
			'my_payment' => $my_payment->id_my_payments,
			'my_paym_filt' => $this->my_paym_filt,
			'my_tip_amount' => ($this->display == 'edit' ? $my_payment->id_my_payments_tip : $this->my_tip_amount),
			'mpv_array' => $this->mpvids,
            'currentVid' => ($this->display == 'edit' ? $my_payment->id_my_payments_vid : ($this->my_tip_amount == 1 ? 1 : 5)),
			'mps_array' => $this->mpsposs,
            'currentSpos' => ($this->display == 'edit' ? $my_payment->id_my_payments_spos :  1),
			'mpk_array' => $this->mpkass,
            'currentKas' => ($this->display == 'edit' ? $my_payment->id_my_payments_kas :  1),
            'currentOrder' => ($this->display == 'edit' ? $my_payment->order :  ''),
            'currentCust' => ($this->display == 'edit' ? $my_payment->id_customer :  0),
            'currentSum' => ($this->display == 'edit' ? $my_payment->summa_all :  0.00),
            'currentPrim' => ($this->display == 'edit' ? $my_payment->prim : ''),
            'currentSumYes' => ($this->display == 'edit' ? $my_payment->summa_yes :  0.00),
            'currentSumNo' => ($this->display == 'edit' ? $my_payment->summa_no :  0.00),
            'recyclable_pack' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
            'gift_wrapping' => (int)Configuration::get('PS_GIFT_WRAPPING'),
            'show_toolbar' => $this->show_toolbar,
            'toolbar_btn' => $this->toolbar_btn,
			'title' => array($this->l('Orders'), $this->l('Create order'))

        ));
        $this->content .= $this->createTemplate('form.tpl')->fetch();
        $this->content .= $this->createTemplate('footer_toolbar.tpl')->fetch();
	}


    /**
     * AdminController::initToolbar() override
     * @see AdminController::initToolbar()
     *
     */
    public function initToolbar()
    {
		                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );

                // Default cancel button - like old back link
                if (!isset($this->no_back) || $this->no_back == false) {
                    $back = Tools::safeOutput(Tools::getValue('back', ''));
                    if (empty($back)) {
                        $back = self::$currentIndex.'&token='.$this->token;
                    }

                    $this->toolbar_btn['cancel'] = array(
                        'href' => $back,
                        'desc' => $this->l('Cancel')
                    );
                }

		switch ($this->action) {
            case 'newp':
				$this->toolbar_title[] = $this->l('Add new incoming');
				$this->my_tip_amount = 1;
				break;
            case 'newr':
				$this->toolbar_title[] = $this->l('Add new expense');
				$this->my_tip_amount = 2;
				break;
		}
		$res = parent::initToolbar();
        return $res;
    }

    public function renderView()
    {
/*     {
        if (!($my_payment = $this->loadObject(true))) {
            return;
        }

        return parent::renderView();
    }
 */    
		$order = new Order(Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            $this->errors[] = Tools::displayError('The order cannot be found within your database.');
        }

        $customer = new Customer($order->id_customer);
        $carrier = new Carrier($order->id_carrier);
        $products = $this->getProducts($order);
        $currency = new Currency((int)$order->id_currency);
        // Carrier module call
        $carrier_module_call = null;
        if ($carrier->is_module) {
            $module = Module::getInstanceByName($carrier->external_module_name);
            if (method_exists($module, 'displayInfoByCart')) {
                $carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
            }
        }

        // Retrieve addresses information
        $addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int)$addressInvoice->id_state);
        }

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int)($addressDelivery->id_state));
            }
        }

        $this->toolbar_title = sprintf($this->l('Order #%1$d (%2$s) - %3$s %4$s'), $order->id, $order->reference, $customer->firstname, $customer->lastname);
        if (Shop::isFeatureActive()) {
            $shop = new Shop((int)$order->id_shop);
            $this->toolbar_title .= ' - '.sprintf($this->l('Shop: %s'), $shop->name);
        }

        // gets warehouses to ship products, if and only if advanced stock management is activated
        $warehouse_list = null;

        $order_details = $order->getOrderDetailList();
        foreach ($order_details as $order_detail) {
            $product = new Product($order_detail['product_id']);

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                && $product->advanced_stock_management) {
                $warehouses = Warehouse::getWarehousesByProductId($order_detail['product_id'], $order_detail['product_attribute_id']);
                foreach ($warehouses as $warehouse) {
                    if (!isset($warehouse_list[$warehouse['id_warehouse']])) {
                        $warehouse_list[$warehouse['id_warehouse']] = $warehouse;
                    }
                }
            }
        }

        $payment_methods = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);
            if (Validate::isLoadedObject($module) && $module->active) {
                $payment_methods[] = $module->displayName;
            }
        }

        // display warning if there are products out of stock
        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1))) {
            $display_out_of_stock_warning = true;
        }

        // products current stock (from stock_available)
        foreach ($products as &$product) {
            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int)$customization['quantity'];
                    }
                }
            }

            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl'], $currency);
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            // if the current stock requires a warning
            if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                $this->displayWarning($this->l('This product is out of stock: ').' '.$product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int)$product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }
        }

        $gender = new Gender((int)$customer->id_gender, $this->context->language->id);

        $history = $order->getHistory($this->context->language->id, false, false, 0, 'admin');

        foreach ($history as &$order_state) {
            $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
        }

        // Smarty assign
        $this->tpl_view_vars = array(
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'gender' => $gender,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null
            ),
            'customerStats' => $customer->getStats(),
            'products' => $products,
            'discounts' => $order->getCartRules(),
            'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(), // Get the sum of total_paid_tax_incl of the order with similar reference
            'total_paid' => $order->getTotalPaid(),
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => Message::getMessagesByOrderId($order->id, true),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $history,
            'states' => OrderState::getOrderStates($this->context->language->id),
            'warehouse_list' => $warehouse_list,
            'sources' => ConnectionsSource::getOrderSources($order->id),
            'currentState' => $order->getCurrentOrderState(),
            'currency' => new Currency($order->id_currency),
            'currencies' => Currency::getCurrenciesByIdShop($order->id_shop),
            'previousOrder' => $order->getPreviousOrderId(),
            'nextOrder' => $order->getNextOrderId(),
            'current_index' => self::$currentIndex,
            'carrierModuleCall' => $carrier_module_call,
            'iso_code_lang' => $this->context->language->iso_code,
            'id_lang' => $this->context->language->id,
            'can_edit' => ($this->tabAccess['edit'] == 1),
            'current_id_lang' => $this->context->language->id,
            'invoices_collection' => $order->getInvoicesCollection(),
            'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
            'payment_methods' => $payment_methods,
            'invoice_management_active' => Configuration::get('PS_INVOICE', null, null, $order->id_shop),
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'HOOK_CONTENT_ORDER' => Hook::exec('displayAdminOrderContentOrder', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_CONTENT_SHIP' => Hook::exec('displayAdminOrderContentShip', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_TAB_ORDER' => Hook::exec('displayAdminOrderTabOrder', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_TAB_SHIP' => Hook::exec('displayAdminOrderTabShip', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
        );

        return parent::renderView();
    }

/*     public function initContent()
    {
        $this->initTabModuleList();
        // toolbar (save, cancel, new, ..)
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        if ($this->display == 'editaddresses' || $this->display == 'addaddress') {
            $this->content .= $this->renderFormAddress();
        } elseif ($this->display == 'edit' || $this->display == 'add') {
            if (!$this->loadObject(true)) {
                return;
            }
            $this->content .= $this->renderForm();
        } elseif ($this->display == 'view') {
            // Some controllers use the view action without an object
            if ($this->className) {
                $this->loadObject(true);
            }
            $this->content .= $this->renderView();
        } elseif (!$this->ajax) {
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }
 */
}
