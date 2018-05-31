<?php

/**
* vkapi module main file.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 1
*/

if (!defined('_PS_VERSION_'))
	exit;

class vkapi extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'vkapi';
		$this->tab = 'social_networks';
		$this->version = '1.0';
		$this->author = 'PrestaLab.Ru';
		$this->need_instance = 0;
		//Ключик из addons.prestashop.com
		$this->module_key='';

		parent::__construct();

		$this->displayName = $this->l('Vkontakte open API');
		$this->description = $this->l('Vkontakte asincronus open api');
	}

	public function install()
	{
		if (!Hook::getIdByName('vkapiInit')) {
			$hook = new Hook();
			$hook->name = 'vkapiInit';
			$hook->title = $this->displayName;
			$hook->description = $this->description;
			$hook->add();
		}
		return (parent::install()
			&& $this->registerHook('header')
		);
	}

	public function uninstall()
	{
		if ($id_hook = Hook::getIdByName('vkapiInit')) {
			$hook = new Hook($id_hook);
			$hook->delete();
		}
		return (parent::uninstall()
			&& Configuration::deleteByName('vkapi_apiid')
		);
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitvkapi'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= $this->displayError($err);;
		}
		$this->_displayForm();
		return $this->_html;
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);
		return $this->toolbar_btn;
	}

	protected function _displayForm()
	{
		$this->_display = 'index';
		
		
		$this->fields_form[0]['form'] = array(
				'legend' => array(
				'title' => $this->l('Settings'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('API ID'),
					'desc' => $this->l('Numerical application identifier'),
					'name' => 'vkapi_apiid',
					'size' => 33,
				),
			
			),
			
			'submit' => array(
				'name' => 'submitvkapi',
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);
		$this->fields_value['vkapi_apiid'] = Configuration::get('vkapi_apiid');

		$helper = $this->initForm();
		$helper->submit_action = '';
		
		$helper->title = $this->displayName;
		
		$helper->fields_value = $this->fields_value;
		$this->_html .= $helper->generateForm($this->fields_form);
		return;
	}

	private function initForm()
	{
		$helper = new HelperForm();
		
		$helper->module = $this;
		$helper->name_controller = 'vkapi';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->toolbar_scroll = true;
		$helper->tpl_vars['version'] = $this->version;
		$helper->tpl_vars['author'] = $this->author;
		$helper->tpl_vars['this_path'] = $this->_path;
		$helper->toolbar_btn = $this->initToolbar();
		
		return $helper;
	}

	private function _postValidation()
	{
		if(Tools::getValue('vkapi_apiid')&&(!Validate::isInt(Tools::getValue('vkapi_apiid'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('API ID');
	}

	private function _postProcess()
	{

		Configuration::updateValue('vkapi_apiid', Tools::getValue('vkapi_apiid'));
		$this->_html .= $this->displayConfirmation($this->l('Settings updated.'));
	}

	public function hookheader($params)
	{
		$this->context->controller->addJS(($this->_path) . 'js/vk-async.min.js');
		if ($vkapi_apiid = Configuration::get('vkapi_apiid')) 
		{
			return '
			<script type="text/javascript">
			VK_async.ready(function () {
			    VK.init({apiId: ' . Configuration::get('vkapi_apiid') . ', onlyWidgets: true});
			    ' . Hook::exec('vkapiInit', array('vkapi_apiid' => $vkapi_apiid)) . '
			});
			</script>';
		}
	}



}