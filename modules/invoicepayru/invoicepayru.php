<?php

class InvoicePayRu extends Module
{
	public function __construct()
	{
		$this->name = 'invoicepayru';
		$this->tab = 'administration';
		$this->version = '0.1';
		$this->author = 'PrestaDev.ru';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Invoice pay');
		$this->description = $this->l('Displayed an invoice pay (Russia)');

		if (!Configuration::get('INVOICEPAYRU_PREFIX'))
			$this->warning = $this->l('Please add prefix');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('orderDetailDisplayed') OR !$this->registerHook('adminOrder'))
			return false;
		Configuration::updateValue('INVOICEPAYRU_PREFIX', 'PX-');

		return true;
	}

	public function uninstall()
	{	
		Configuration::deleteByName('INVOICEPAYRU_S');
		Configuration::deleteByName('INVOICEPAYRU_PREFIX');
		Configuration::deleteByName('INVOICEPAYRU_CALLNDS');
		Configuration::deleteByName('INVOICEPAYRU_SHOWL');
		return parent::uninstall();
	}

	public function getContent()
	{
		if ($invoice = Tools::getValue('invoice'))
		{
			include_once($this->local_path.'invoice.php');
			return SPDF::invoice((int)$invoice);
		}

		if (Tools::isSubmit('submit'))
		{
			Configuration::updateValue('INVOICEPAYRU_S', Tools::getValue('INVOICEPAYRU_S'));
			Configuration::updateValue('INVOICEPAYRU_A', Tools::getValue('INVOICEPAYRU_A'));
			Configuration::updateValue('INVOICEPAYRU_FA', Tools::getValue('INVOICEPAYRU_FA'));
			Configuration::updateValue('INVOICEPAYRU_I', Tools::getValue('INVOICEPAYRU_I'));
			Configuration::updateValue('INVOICEPAYRU_K', Tools::getValue('INVOICEPAYRU_K'));
			Configuration::updateValue('INVOICEPAYRU_C', Tools::getValue('INVOICEPAYRU_C'));
			Configuration::updateValue('INVOICEPAYRU_B', Tools::getValue('INVOICEPAYRU_B'));
			Configuration::updateValue('INVOICEPAYRU_CB', Tools::getValue('INVOICEPAYRU_CB'));
			Configuration::updateValue('INVOICEPAYRU_AB', Tools::getValue('INVOICEPAYRU_AB'));
			Configuration::updateValue('INVOICEPAYRU_PREFIX', Tools::getValue('INVOICEPAYRU_PREFIX'));
			Configuration::updateValue('INVOICEPAYRU_CALLNDS', (int)Tools::isSubmit('INVOICEPAYRU_CALLNDS'));
			Configuration::updateValue('INVOICEPAYRU_SHOWL', (int)Tools::isSubmit('INVOICEPAYRU_SHOWL'));
			Configuration::updateValue('INVOICEPAYRU_ADDDELIVERY', (int)Tools::isSubmit('INVOICEPAYRU_ADDDELIVERY'));
			Configuration::updateValue('INVOICEPAYRU_TEXT', Tools::getValue('INVOICEPAYRU_TEXT'));
			Configuration::updateValue('INVOICEPAYRU_GD', Tools::getValue('INVOICEPAYRU_GD'));
			Configuration::updateValue('INVOICEPAYRU_FIO', Tools::getValue('INVOICEPAYRU_FIO'));
			Configuration::updateValue('INVOICEPAYRU_FP', Tools::getValue('INVOICEPAYRU_FP'));
		}

		return '
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Settings').'</legend>
				<label>'.$this->l('SELLER:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_S" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_S', Configuration::get('INVOICEPAYRU_S')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' ЗАО "Милана"
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('REG ADRESS:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_A" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_A', Configuration::get('INVOICEPAYRU_A')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 129366, Москва, Ракетный бульвар, 17
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('ACTUAL ADRESS:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_FA" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_FA', Configuration::get('INVOICEPAYRU_FA')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 129366, Москва, Ракетный бульвар, 17
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('INN:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_I" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_I', Configuration::get('INVOICEPAYRU_I')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 7717027908
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('KPP:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_K" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_K', Configuration::get('INVOICEPAYRU_K')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 671010011
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('R/CH:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_C" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_C', Configuration::get('INVOICEPAYRU_C')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 40702810500005042124
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('BIK:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_B" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_B', Configuration::get('INVOICEPAYRU_B')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 044585272
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('K/CH:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_CB" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_CB', Configuration::get('INVOICEPAYRU_CB')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' 30101810800000000272
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('BANK ADRESS:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_AB" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_AB', Configuration::get('INVOICEPAYRU_AB')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' АКБ "Нефтепромбанк" г.Москва
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('PREFIX:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_PREFIX" value="'.Tools::getValue('INVOICEPAYRU_PREFIX', Configuration::get('INVOICEPAYRU_PREFIX')).'" /></div>
					'.$this->l('Sample: PX-').'
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Show logo:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><input type="checkbox" value="1" name="INVOICEPAYRU_SHOWL" '.(Configuration::get('INVOICEPAYRU_SHOWL') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Show logo in invoice').'
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Calculate NDS:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><input type="checkbox" value="1" name="INVOICEPAYRU_CALLNDS" '.(Configuration::get('INVOICEPAYRU_CALLNDS') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Calculate NDS in the script').'
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Add delivery:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><input type="checkbox" value="1" name="INVOICEPAYRU_ADDDELIVERY" '.(Configuration::get('INVOICEPAYRU_ADDDELIVERY') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Add to invoice delivery').'
				</div>

				<!-- Начало правок -->

				<label>'.$this->l('Должность:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_GD" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_GD', Configuration::get('INVOICEPAYRU_GD')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' Генеральный директор или Индивидуальный предприниматель
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('ФИО директора:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_FIO" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_FIO', Configuration::get('INVOICEPAYRU_FIO')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' Иванов В.В.
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('Название файла с изображением печати:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICEPAYRU_FP" value="'.htmlentities(Tools::getValue('INVOICEPAYRU_FP', Configuration::get('INVOICEPAYRU_FP')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' Например печать.png, файл должен лежать в папке /public_html/img
				</div>
				<div class="clear">&nbsp;</div>

				<!-- конец -->

				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Add text:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><textarea name="INVOICEPAYRU_TEXT" rows="4" cols="53">'.htmlentities(Tools::getValue('INVOICEPAYRU_TEXT', Configuration::get('INVOICEPAYRU_TEXT')), ENT_COMPAT, 'UTF-8').'</textarea>
				</div>
				<div class="clear">&nbsp;</div>
				<center><input type="submit" name="submit" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>
		<div class="clear">&nbsp;</div>';
	}

	function hookOrderDetailDisplayed($params)
	{
		global $smarty;

		if (version_compare(_PS_VERSION_,'1.5','<'))
		{
		?><script type="text/javascript">
//			$('a[href*="pdf-invoice.php"]').parent('p').before('<p><img src="/img/admin/pdf.gif" alt="" class="icon" /> <a href="/modules/invoicepayru/invoice.php?id_order=<?php echo (int)$params['order']->id; ?>">Скачать счет на оплату</a></p>');
		</script><?php 
		}
		else
		{
		?><script type="text/javascript">
//			$('.info-order').find('p:last').after('<p><img src="/img/admin/pdf.gif" alt="" class="icon" /> <a href="/modules/invoicepayru/invoice.php?id_order=<?php echo (int)$params['order']->id; ?>">Скачать счет на оплату</a></p>');
		</script><?php 
		}
	}

	function hookAdminOrder($params)
	{
		?><script type="text/javascript">
		$(document).ready(function(){
//			$('a[href*="javascript:window.print()"]').after(' <a href="<?php echo $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&invoice=' . $params['id_order']; ?>" class="btn btn-default"><i class="icon-file"></i> Счёт на оплату</a>');
		});
		</script><?php
	}
}