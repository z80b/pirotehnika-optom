<?php

if(!defined('_CAN_LOAD_FILES_'))	exit;

class ulogin extends Module
{
	private $_html ='';
	public
	function __construct()
	{
		$this->name = 'ulogin';
		$this->tab = 'social_networks';
		$this->version = 0.8;
		$this->author = 'Elcommerce';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6.10');
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Ulogin');
		$this->description = $this->l('Authentication from social networks');
	}


	public function install()
{
  if (Shop::isFeatureActive())
    Shop::setContext(Shop::CONTEXT_ALL);

  return parent::install() &&
    $this->registerHook('auth') && $this->registerHook('displayNav') && $this->registerHook('displayHeader');


}
	private
	function _displayabout()
	{

		$this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset class="space">
		<legend><img src="../img/admin/email.gif" /> ' . $this->l('Information') . '</legend>
		<div id="dev_div">
		<span><b>' . $this->l('Version') . ':</b> ' . $this->version . '</span><br>
		<span><b>' . $this->l('Developer') . ':</b> <a class="link" href="http://elcommerce.com.ua" target="_blank">savvato</a><br>
		<span><b>' . $this->l('Описание') . ':</b> <a class="link" href="http://support.elcommerce.com.ua/kb/index.php" target="_blank">Наша база знаний</a><br><br>
		<p style="text-align:center"><a href="http://elcommerce.com.ua/"><img src="http://elcommerce.com.ua/img/m/logo.png" alt="Электронный учет коммерческой деятельности" /></a>

		</div>
		</fieldset>
		';
	}

	public
	function postProcess()
	{
		require_once(_PS_MODULE_DIR_.'ulogin/classes/UloginAuth.php');
		require_once(_PS_MODULE_DIR_.'ulogin/classes/Init.php');
		if(Tools::isSubmit('submitDisp')){


			Configuration::UpdateValue('ULOGIN_DISPLAY', Tools::GetValue('display_type'));

			$this->_html .= '
			<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="" title="" />
			Настройки обновлены
			</div>';
		}
		if(Tools::isSubmit('submitProv')){

			Configuration::UpdateValue('ULOGIN_PROVIDER_MAIN', Tools::GetValue('provider_main'));
			Configuration::UpdateValue('ULOGIN_PROVIDER', Tools::GetValue('provider'));
			Configuration::UpdateValue('ID_VIDGET', Tools::GetValue('ID_vidget'));
			$validate   = new Init();
			$valid_prov = array();
			$input = Tools::GetValue('provider_main');
			$output= array();


			$valid_prov_sub = array();
			$input_sub = Tools::GetValue('provider');
			$output_sub= array();

			if(!empty($input)){
				$arr_prov = explode(",", $input);
				foreach($arr_prov as $child){
					if(!empty($child) AND in_array(trim($child), $validate->prov))					$valid_prov[] = trim($child);
				}
				if($size = count($valid_prov) > 7)
				{
					for($i = 0, $size = count($valid_prov); $i < 7; ++$i)
					{
						$output[$i] = $valid_prov[$i];
					}
				}
				else
				{
					$output = $valid_prov;
				}
			}
			$_POST['provider_main'] = trim(implode(',', $output));

			if(!empty($input_sub)){
				$arr_prov_sub = explode(",", $input_sub);
				foreach($arr_prov_sub as $child_sub){
					if(!empty($child_sub))					if(in_array(trim($child_sub), $validate->prov))					$valid_prov_sub[] = trim($child_sub);


				}
				$output_sub = $valid_prov_sub;

			}

			$_POST['provider'] = trim(implode(',', $output_sub));

    $this->_html .= '
			<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="" title="" />
			Настройки обновлены
			</div>';

		}
	if(Tools::isSubmit('submitPref')){
        $adr = ((isset($_POST['adr']))&&($_POST['adr'] == '1'))? 1 : 0;
            Configuration::updateValue('_ADR_', $adr);
		$page = ((isset($_POST['page']))&&($_POST['page'] == '1'))? 1 : 2;
		Configuration::updateValue('_PAGE_', $page);
	$this->_html .= '
			<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="" title="" />
			Настройки обновлены
			</div>';

    }

	}

	//}
	public
	function getContent()
	{

		$page = Configuration::get('_PAGE_');
		if (empty($page)) $page = 1;
		if($page == 1)
		{
			$page1 = "checked"; $page2 = "";
		}
		if($page == 2)
		{
			$page1 = ""; $page2 = "checked";
		}
		$this->_html .= '<h2>'.$this->l('Authentication from social networks (servise Ulogin)').'</h2>';
		$this->postProcess();
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset><legend>'.$this->l('Displey').'</legend>
		<label>'.$this->l('Displeyed:').'</label>
		<div class="margin-form"><select name="display_type">
		<option value="2" '.(Tools::GetValue('display_type',Configuration::get('ULOGIN_DISPLAY')) == 2 ? 'selected="selected"' : '').'>'.$this->l('Vidget_ID').'</option>
		<option value="1" '.( Tools::GetValue('display_type',Configuration::get('ULOGIN_DISPLAY')) == 1 ? 'selected="selected"' : '').'>'.$this->l('Panel').'</option>
		<option value="0" '.(Tools::GetValue('display_type',Configuration::get('ULOGIN_DISPLAY')) == 0 ? 'selected="selected"' : '').'>'.$this->l('Window').'</option>
		</select></div>


		<div class="margin-form"><input class="button" type="submit" name="submitDisp" value="'.$this->l('Save').'" /></div>
		</fieldset>
		</form>
		';
		if((Configuration::get('ULOGIN_DISPLAY')) == 1)
		{
			$this->_html .= '

			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset><legend>'.$this->l('Providers').'</legend>
			<label>'.$this->l('Maiin providers:').'</label>
			<div class="margin-form">
			<input type="text" name="provider_main" style="width: 600px" value="'.Tools::GetValue('provider_main',Configuration::get('ULOGIN_PROVIDER_MAIN')).'" />
			<p class="clear">'.$this->l('Providers authorization separated by commas. For example:').'vkontakte,odnoklassniki,mailru,facebook,twitter,google,yandex,livejournal,lastfm,</br>linkedin,liveid,soundcloud,steam,tumblr,flickr,vimeo,youtube,webmoney,foursquare,googleplus,dudu,openid.</p>
			</div>
			<label>'.$this->l('Providers of additional:').'</label>
			<div class="margin-form">
			<input type="text" name="provider" style="width: 600px" value="'.Tools::GetValue('provider',Configuration::get('ULOGIN_PROVIDER')).'" />
			<p class="clear">'.$this->l('Providers authorization separated by commas. For example:').' vkontakte,odnoklassniki,mailru,facebook,twitter,google,yandex,livejournal,lastfm,</br>linkedin,liveid,soundcloud,steam,tumblr,flickr,vimeo,youtube,webmoney,foursquare,googleplus,dudu,openid. To select all leave blank</p>
			</div>
			<div class="margin-form"><input class="button" type="submit" name="submitProv" value="'.$this->l('Save').'" /></div>
			</fieldset>
			</form>
			';

		}else{
			if((Configuration::get('ULOGIN_DISPLAY')) == 2){
			$this->_html .= '

			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset><legend>'.$this->l('Providers').'</legend>
			<label>'.$this->l('ID_vidget:').'</label>
			<div class="margin-form">
			<input type="text" name="ID_vidget" style="width: 300px" value="'.Tools::GetValue('ID_vidget',Configuration::get('ID_VIDGET')).'" />
			<p class="clear">'.$this->l('viddget id from LK ulogin.ru:').'</p>
			</div>

			<div class="margin-form"><input class="button" type="submit" name="submitProv" value="'.$this->l('Save').'" /></div>
			</fieldset>
			</form>
			';

				}

			}
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset><legend>'.$this->l('Preferens').'</legend>

        <label>'.$this->l('Generate the client\'s address').'</label>
				<div class="margin-form">
					<input type="checkbox" name="adr" value="1" ' . (Tools::getValue('adr', Configuration::get('_ADR_'))? 'checked="checked" ' : '' ) . ' />
          <p class="clear">'.$this->l('Check the box to auto generate the address of the client. This is useful for shops selling electronic goods.:').'</p>
       </div>
	    <label>'.$this->l('After logging redirect the client').'</label>
		<div class="margin-form">
		<input type="radio" name="page" value="1"  ' .$page1.' />'.$this->l('always in your personal account').' &nbsp;&nbsp;
		<input type="radio" name="page" value="2"  ' .$page2.'/> '.$this->l('to the payment page, if there are items in your shopping cart').'

		</div>

		<div class="margin-form"><input class="button" type="submit" name="submitPref" value="'.$this->l('Save').'" /></div>
		</fieldset>
		</form>
		';
		$this->_displayabout();
		return $this->_html;
	}
	public function hookDisplayNav($params)
	{
		$this->smarty->assign(array(

			'logged' => $this->context->customer->isLogged(),

		));
		return $this->display(__FILE__, 'nav.tpl');
	}
	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'ulogin.css', 'all');
	}
	function hookauth($params)
	{
		require_once(_PS_MODULE_DIR_.'ulogin/classes/UloginAuth.php');
		require_once(_PS_MODULE_DIR_.'ulogin/classes/Init.php');
		$init = new Init();
		$init->run();
		global $smarty;
		$smarty->assign('providers_set',Configuration::get('ULOGIN_PROVIDER_MAIN'));
		$smarty->assign('providers_sub',Configuration::get('ULOGIN_PROVIDER'));
		$id_vidget = '<div id="uLogin_'.Configuration::get('ID_VIDGET').'" data-uloginid="'.Configuration::get('ID_VIDGET').'"></div>';
		$smarty->assign('id_vidget',$id_vidget);
		switch (Configuration::get('ULOGIN_DISPLAY'))
		{
		case 0:
			return $this->display(__FILE__, 'ulogin_window.tpl');
		break;
		case 1:
			return $this->display(__FILE__, 'ulogin_panel.tpl');
		break;
		case 2:
			return $this->display(__FILE__, 'ulogin_widget.tpl');
		break;
		}


	}



}



?>
