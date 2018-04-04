<?php
	
class ExReg extends Module
{
	public function __construct()
	{
		$this->name = 'exreg';
		$this->tab = 'administration';
		$this->version = '0.3';
		$this->author = 'PrestaDev.ru';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Register module');
		$this->description = $this->l('Show details form at registration form and account block');
	}

	public function install()
	{
		if (!parent::install() || 
			!$this->registerHook('actionCustomerAccountAdd') || 
			!$this->registerHook('displayCustomerAccountForm') || 
			!$this->registerHook('displayCustomerAccount') || 
			!$this->registerHook('displayMyAccountBlock') || 
			!$this->registerHook('myAccountBlock') || 
			!$this->registerHook('displayAdminCustomers') || 
			!$this->registerHook('displayAdminOrder') || 
			!$this->registerHook('displayBackOfficeFooter') || 
			!$this->registerHook('displayHeader') || 
			!$this->registerHook('actionAdminControllerSetMedia') || 
			!Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'exreg` (
				`id_customer` int(10) unsigned NOT NULL default 0, 
				`org_name` varchar(64) NOT NULL, 
				`isnds` tinyint(1) NOT NULL default 0, 
				`org_ur_addr` varchar(128) NOT NULL, 
				`org_post_addr` varchar(128) NOT NULL, 
				`inn` varchar(21) NOT NULL, 
				`kpp` varchar(9) NOT NULL, 
				`bank` varchar(128) NOT NULL, 
				`bik` varchar(9) NOT NULL, 
				`rs` varchar(24) NOT NULL, 
				`ks` varchar(20) NOT NULL, 
				`ogrn` varchar(13) NOT NULL, 
				`okpo` varchar(13) NOT NULL, 
				PRIMARY KEY (`id_customer`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
			))
			return false;
		return true;
	}

	public function uninstall()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'exreg`');
		if (!parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submit'))
			Configuration::updateValue('EXREG_SHOW_WITH_REG', (int)Tools::isSubmit('EXREG_SHOW_WITH_REG'));

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->fields_value = Configuration::getMultiple(array(
			'EXREG_SHOW_WITH_REG',
		));

		return $this->_html . $helper->generateForm(array(array(
			'form' => array(
				'legend' => array(
					'title' => 'Настройки',
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => 'Показать форму',
						'name' => 'EXREG_SHOW_WITH_REG',
						'desc' => 'Показывать форму при регистрации клиента.',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => 'Да'
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => 'Нет'
							)
						),
					),
				),
				'submit' => array(
					'title' => 'Сохранить настройки',
				),
			),
		)));
	}

	public function hookDisplayCustomerAccountForm($params)
	{
		if (!Configuration::get('EXREG_SHOW_WITH_REG'))
			return;
		if (Configuration::get('PS_HARD_REGISTRATION', null, null, Context::getContext()->shop->id)) {
			if (Configuration::get('PS_EASY_CODE_REGISTRATION', null, null, Context::getContext()->shop->id)) {
				return $this->display(__FILE__, 'create-account-form-hard-promo.tpl');
			} else {
				return $this->display(__FILE__, 'create-account-form-hard.tpl');
			}	
		} else {
			return $this->display(__FILE__, 'create-account-form.tpl');
		}	
	}

	public function hookActionCustomerAccountAdd($params)
	{
		if (!Configuration::get('EXREG_SHOW_WITH_REG'))
			return;

		if (!Tools::isSubmit('iex'))
			return;

		if (Validate::isLoadedObject($params['newCustomer']))
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'exreg', array(
				'id_customer' => $params['newCustomer']->id,
				'org_name' => pSQL(Tools::getValue('org_name')),
				'isnds' => pSQL(Tools::getValue('isnds')),
				'org_ur_addr' => pSQL(Tools::getValue('org_ur_addr')),
				'org_post_addr' => pSQL(Tools::getValue('org_post_addr')),
				'inn' => pSQL(Tools::getValue('inn')),
				'kpp' => pSQL(Tools::getValue('kpp')),
				'bank' => pSQL(Tools::getValue('bank')),
				'bik' => pSQL(Tools::getValue('bik')),
				'rs' => pSQL(Tools::getValue('rs')),
			), 'INSERT');
	}

	public function hookDisplayCustomerAccount($params)
	{
		if (Context::getContext()->customer->isLogged() && $this->smarty->assign('in_footer', false))
			return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		if (Context::getContext()->customer->isLogged() && $this->smarty->assign('in_footer', true))
			return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'exreg.css', 'all');
	}

	public function hookActionAdminControllerSetMedia()
	{
		if (get_class($this->context->controller) == 'AdminCustomersController')
			$this->context->controller->addJs($this->_path.'exreg.js');
	}
	
	public function hookDisplayBackOfficeFooter($params)
	{
		if (get_class($this->context->controller) != 'AdminCustomersController')
			return;
		if(!Tools::getIsset('id_customer'))
			return;

		if (Tools::isSubmit('submitEx'))
		{
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'exreg` WHERE `id_customer` = '.(int)Tools::getValue('id_customer'));
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'exreg', array(
				'id_customer' => (int)Tools::getValue('id_customer'),
				'org_name' => pSQL(Tools::getValue('org_name')),
				'isnds' => pSQL(Tools::getValue('isnds')),
				'org_ur_addr' => pSQL(Tools::getValue('org_ur_addr')),
				'org_post_addr' => pSQL(Tools::getValue('org_post_addr')),
				'inn' => pSQL(Tools::getValue('inn')),
				'kpp' => pSQL(Tools::getValue('kpp')),
				'bank' => pSQL(Tools::getValue('bank')),
				'bik' => pSQL(Tools::getValue('bik')),
				'rs' => pSQL(Tools::getValue('rs')),
			), 'INSERT');
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = 'exreg';
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submit';
		$helper->fields_value = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'exreg` WHERE `id_customer` = '.(int)Tools::getValue('id_customer'));
		
		return $helper->generateForm(array(array(
			'form' => array(
				'legend' => array(
					'title' => 'Реквизиты',
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => 'Наименование',
						'name' => 'org_name',
						'class' => 'fixed-width-xxl',
					),
				   array(
						'type' => 'switch',
						'label' => 'Общая система налогооблажения (с НДС)',
						'name' => 'isnds',
						'required' => true,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
						'hint' => $this->l('Обязательно нажмите Да, если данная организация работает с НДС.')
					),
					array(
						'type' => 'text',
						'label' => 'ИНН',
						'name' => 'inn',
						'class' => 'fixed-width-xxl',
					),
					array(
						'type' => 'text',
						'label' => 'КПП',
						'name' => 'kpp',
						'class' => 'fixed-width-xxl',
					),
					array(
						'type' => 'text',
						'label' => 'Юридический адрес',
						'name' => 'org_ur_addr',
						'class' => 'fixed-width-xxl',
					),
					array(
						'type' => 'text',
						'label' => 'Фактический адрес',
						'name' => 'org_post_addr',
						'class' => 'fixed-width-xxl',
					),
					array(
						'type' => 'text',
						'label' => 'Р/Счет',
						'name' => 'rs',
						'class' => 'fixed-width-xxl',
					),
					array(
						'type' => 'text',
						'label' => 'В банке',
						'name' => 'bank',
						'class' => 'fixed-width-xxl',
					),
					array(
						'type' => 'text',
						'label' => 'БИК',
						'name' => 'bik',
						'class' => 'fixed-width-xxl',
					),
				),
				'submit' => array(
					'title' => 'Сохранить настройки',
					'name' => 'submitEx',
				),
			),
		)));
	}
}