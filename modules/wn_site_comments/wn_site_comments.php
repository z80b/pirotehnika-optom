<?php

if (!defined('_PS_VERSION_'))
	exit;


require_once _PS_MODULE_DIR_.'wn_site_comments/classes/WNInfoBlock.php';
class wn_site_comments extends Module
{
	public $html = '';

	public function __construct()
	{
		$this->name = 'wn_site_comments';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Dulco';
		$this->bootstrap = true;
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Комментарии о сайте');
		$this->description = $this->l('Добавляет страницу комментариев на ваш сайт.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return 	parent::install() &&
				$this->installDB() &&
				$this->registerHook('header') &&
				$this->registerHook('leftcolumn') &&
				$this->disableDevice(Context::DEVICE_TABLET | Context::DEVICE_MOBILE);
	}

	public function installDB()
	{
		$return = true;

		$return &= Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'commtemp` (
				`id` INT( 12 ) NOT NULL AUTO_INCREMENT,
				`name` text NOT NULL,
				`email` varchar(128) NOT NULL,
				`parent` int(11) NOT NULL DEFAULT "0",
				`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`message` text NOT NULL,
				`title` text,
                `post` text,
				 PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
		);

		return $return;
	}

	public function uninstall()
	{
		return parent::uninstall() && $this->uninstallDB();
	}

	public function uninstallDB($drop_table = true)
	{
		$ret = true;
		if($drop_table)
			$ret &=  Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'commtemp`');

		return $ret;
	}

	public function getContent()
	{
		$id_info = (int)Tools::getValue('id');
		 if (Tools::isSubmit('deletewn_site_comments'))
		{
			$info = new WNInfoBlock((int)$id_info);
			$info->delete();
			$this->_clearCache('wn_site_comments.tpl');
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else
        {	
            $this->html .= $this->renderInfo();		
			$this->html .= $this->renderList();
			return $this->html;
		}
		

	}
    public function renderInfo()
	{
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;	
		$helper->languages = $this->context->controller->getLanguages();
		$helper->default_form_language = (int)$this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->identifier = $this->identifier;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');	
		
		$context = Context::getContext();
		
        $languages = Language::getLanguages();
		$shops = Shop::getShops(true, null, true);
		$output = '';
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Информация'),
					'icon' => 'icon-info',
				),
				'description' => '<div id="dev_div">
			    <p><a href="http://webnewbie.ru/"><img src="'.$this->_path.'logo.png" alt="Бесплатные модули и шаблоны для PrestaShop"/></a></p>
				<span><strong>Версия: </strong>' . $this->version . '</span><br>
				<span><strong>Разработка:</strong> <a class="link" href="mailto:dulco@webnewbie.ru" target="_blank">' . $this->author . '</a><br>
				<span><strong>Ресурс:</strong> <a class="link" href="http://webnewbie.ru/" target="_blank">webnewbie.ru</a><br>
				</div>',
				
			)
		);
		
		return $helper->generateForm(array($fields_form));
	}
	


	

	protected function renderList()
	{
		$this->fields_list = array();
		$this->fields_list['id'] = array(
				'title' => $this->l('ID'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);
		$this->fields_list['name'] = array(
				'title' => $this->l('Имя ответчика'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);
		$this->fields_list['title'] = array(
				'title' => $this->l('Имя'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);
		$this->fields_list['post'] = array(
				'title' => $this->l('Сообщение'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);	
        $this->fields_list['email'] = array(
				'title' => $this->l('Email'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);
		$this->fields_list['message'] = array(
				'title' => $this->l('ответ'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);
        
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = false;
		$helper->identifier = 'id';
		$helper->actions = array('delete');
		$helper->show_toolbar = true;
		$helper->imageType = 'jpg';
		$helper->toolbar_btn['new'] = array(
			'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add new')
		);

		$helper->title = $this->displayName;
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		$content = $this->getListContent($this->context->language->id);

		return $helper->generateList($content, $this->fields_list);
	}
    protected function getGravatar($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = 'SELECT * FROM `'._DB_PREFIX_.'commtemp`  ORDER BY `id` DESC';

		

		$gravatar = Db::getInstance()->executeS($sql);

		foreach ($gravatar as $key => $value)
			
			$gravatar = md5(trim($value['email']));

		return $gravatar;
	}
	protected function getListContent($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = 'SELECT * FROM `'._DB_PREFIX_.'commtemp`  ORDER BY `id` DESC';

		

		$content = Db::getInstance()->executeS($sql);

		foreach ($content as $key => $value)
			$content[$key]['name'] = substr(strip_tags($value['name']), 0, 200);
			

		return $content;
	}

	private function _getLastComent()
	{
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'commtemp` ORDER BY `id` DESC LIMIT 0,3');
	}
    public static function sendRequest($postdata)
	{
		global $cookie;

		$message = $postdata['comm_q'];
		$name = $postdata['comm_name'];
		$email = $postdata['comm_email'];
		
        
		
		if (!Validate::isMessage($message))
			return 'mex'; //invalid message
		if (!Validate::isGenericName($name))
			return 'name'; //invalid message
		if (!Validate::isEmail($email))
			return 'mail'; //invalid message	

		$data = array('post' => pSQL($message), 'email' => pSQL($email), 'title' => pSQL($name),  'date' => date('Y-m-d'));
		
		

		

		if(!Db::getInstance()->insert('commtemp', $data))
			return 'err';

		if(version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$context = Context::getContext();
			
            Mail::Send($context->language->id, 'new_comment', Mail::l('Новый комментарий'),
				array(
					'{message}' => $message,
					
					),
				strval(Configuration::get('PS_SHOP_EMAIL')), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), NULL, NULL, NULL, dirname(__FILE__).'/mails/');
			return true;			
		} 
		

	}
	public function hookLeftColumn($params)
	{
		
		$gravatar = $this->getGravatar($this->context->language->id);
			
			$this->context->smarty->assign(array(
			'comments_dir' => $this->context->link->getModuleLink('wn_site_comments', 'comments'),
			'comments' => $this->_getLastComent(),
			'gravatar' => $gravatar
			));
		

		return $this->display(__FILE__, 'wn_site_comments.tpl');
	}
    public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'style.css', 'all');
		$this->context->controller->addJS($this->_path.'comments.js');
		$this->context->controller->addJS($this->_path.'post.js');
		$this->context->controller->addJS($this->_path.'js/validation.js');
		$this->context->controller->addJS($this->_path.'js/wn_site_comments.js');
		$this->context->controller->addJS($this->_path.'js/jquery.validate.min.js');
		
		

		
	}
	

	
}
