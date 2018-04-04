<?php

class invoiceTorg extends Module
{
	public function __construct()
	{
		$this->name = 'invoicetorg';
		$this->tab = 'administration';
		$this->version = '0.2';
		$this->author = 'PrestaDev.ru';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Invoice torg12');
		$this->description = $this->l('Displayed an invoice torg12 (Russia)');

		if (!Configuration::get('INVOICETORGRU_SH'))
			$this->warning = $this->l('Please add shipper');

		if (!Configuration::get('INVOICETORGRU_S'))
			$this->warning = $this->l('Please add seller');

		if (!Configuration::get('INVOICETORGRU_PREFIX'))
			$this->warning = $this->l('Please add prefix');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('orderDetailDisplayed') OR !$this->registerHook('adminOrder'))
			return false;
		Configuration::updateValue('INVOICETORGRU_PREFIX', 'TX-');

		return true;
	}

	public function uninstall()
	{	
		Configuration::deleteByName('INVOICETORGRU_SH');
		Configuration::deleteByName('INVOICETORGRU_S');
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
			Configuration::updateValue('INVOICETORGRU_SH', Tools::getValue('INVOICETORGRU_SH'));
			Configuration::updateValue('INVOICETORGRU_S', Tools::getValue('INVOICETORGRU_S'));
			Configuration::updateValue('INVOICETORGRU_PREFIX', Tools::getValue('INVOICETORGRU_PREFIX'));
			Configuration::updateValue('INVOICETORGRU_CALLNDS', (int)Tools::isSubmit('INVOICETORGRU_CALLNDS'));
			Configuration::updateValue('INVOICETORGRU_NONDS', (int)Tools::isSubmit('INVOICETORGRU_NONDS'));
			Configuration::updateValue('INVOICETORGRU_ADDDELIVERY', (int)Tools::isSubmit('INVOICETORGRU_ADDDELIVERY'));
		}

		return '
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Settings').'</legend>
				<label>'.$this->l('SHIPPER:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICETORGRU_SH" value="'.htmlentities(Tools::getValue('INVOICETORGRU_SH', Configuration::get('INVOICETORGRU_S')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' ЗАО "Милана", 661201, Красноярск, Ракетный бульвар 17, оф. 404, <br />
					ИНН 7717027908/671010011, тел. +79504047448, р/с 40702810600005042124 в <br />
					АКБ "Нефтепромбанк", г.Красноярск, БИК 044585272, корр/с 30101810800000000272
				</div>
				<div class="clear">&nbsp;</div>

				<label>'.$this->l('SELLER:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICETORGRU_S" value="'.htmlentities(Tools::getValue('INVOICETORGRU_S', Configuration::get('INVOICETORGRU_S')), ENT_COMPAT, 'UTF-8').'" /></div>
					'.$this->l('Sample:').' ЗАО "Милана", 661201, Красноярск, Ракетный бульвар 17, оф. 404, <br />
					ИНН 7717027908/671010011, р/с 40702810600005042124 в <br />
					АКБ "Нефтепромбанк", г.Красноярск, БИК 044585272, корр/с 30101810800000000272
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('PREFIX:').'</label>
				<div class="margin-form">
					<div><input type="text" size="25" name="INVOICETORGRU_PREFIX" value="'.Tools::getValue('INVOICETORGRU_PREFIX', Configuration::get('INVOICETORGRU_PREFIX')).'" /></div>
					'.$this->l('Sample: TX-').'
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Calculate NDS:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><input type="checkbox" value="1" name="INVOICETORGRU_CALLNDS" '.(Configuration::get('INVOICETORGRU_CALLNDS') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Calculate NDS in the script').'
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Write NO NDS:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><input type="checkbox" value="1" name="INVOICETORGRU_NONDS" '.(Configuration::get('INVOICETORGRU_NONDS') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Write: Without NDS').'
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Add delivery:').'</label>
				<div class="margin-form">
					<div style="margin-top:4px"><input type="checkbox" value="1" name="INVOICETORGRU_ADDDELIVERY" '.(Configuration::get('INVOICETORGRU_ADDDELIVERY') ? 'checked' : '').' />&nbsp;'.$this->l('Yes').'</div>'.$this->l('Add to invoice delivery').'
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
			$('a[href*="pdf-invoice.php"]').parent('p').before('<p><img src="/img/admin/pdf.gif" alt="" class="icon" /> <a href="/modules/invoicetorg/invoice.php?id_order=<?php echo (int)$params['order']->id; ?>">Скачать товарную накладную</a></p>');
		</script><?php 
		}
		else
		{
		?><script type="text/javascript">
			$('.info-order').find('p:last').after('<p><img src="/img/admin/pdf.gif" alt="" class="icon" /> <a href="/modules/invoicetorg/invoice.php?id_order=<?php echo (int)$params['order']->id; ?>">Скачать товарную накладную</a></p>');
		</script><?php 
		}
	}

	function hookAdminOrder($params)
	{
		?><script type="text/javascript">
		$(document).ready(function(){
			$('a[href*="javascript:window.print()"]').after(' <a href="<?php echo $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&invoice=' . $params['id_order']; ?>" class="btn btn-default"><i class="icon-file"></i> Товарная накладная</a>');
		});
		</script><?php	
	}

}