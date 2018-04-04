<?php

class ExRegAccountModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;

	public function initContent()
	{
		parent::initContent();

		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&redirect=module&module=exreg&action=account');

		if (Context::getContext()->customer->id)
		{
			$this->context->smarty->assign('company', Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'exreg` WHERE `id_customer` = '.(int)Context::getContext()->customer->id));

			$this->setTemplate('exreg-account.tpl');
		}
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitCompany') && Context::getContext()->customer->isLogged())
		{
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'exreg` WHERE `id_customer` = '.(int)Context::getContext()->customer->id);
			if (Db::getInstance()->AutoExecute(_DB_PREFIX_.'exreg', array('id_customer' => (int)Context::getContext()->customer->id, 'org_name' => pSQL(Tools::getValue('org_name')),'isnds' => pSQL(Tools::getValue('isnds')), 'org_ur_addr' => pSQL(Tools::getValue('org_ur_addr')), 'org_post_addr' => pSQL(Tools::getValue('org_post_addr')), 'inn' => pSQL(Tools::getValue('inn')), 'kpp' => pSQL(Tools::getValue('kpp')), 'bank' => pSQL(Tools::getValue('bank')), 'bik' => pSQL(Tools::getValue('bik')), 'rs' => pSQL(Tools::getValue('rs'))), 'INSERT'))
					Context::getContext()->smarty->assign('confirmation', 1);
		}
	}
}