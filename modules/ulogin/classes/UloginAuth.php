<?php
class UloginAuth
{

	protected
	function ulogin()
	{
		global $cart,$cookie;
		if(Tools::getValue('token')){

			$link = new Link();
			$s    = file_get_contents('https://ulogin.ru/token.php?token=' . Tools::getValue('token') . '&host=' . $link->getPageLink('authentication.php'));

			$data = json_decode($s, true);
			if(@$data ['error']) return FALSE;
			//d($data);
			//Configuration::UpdateValue('ULOGIN_MAIN',Tools::getValue('token') );
			$email          = trim($data['email']);
			$passwd         = md5($data['first_name'].$data['last_name']);
			$customer       = new Customer();
			$authentication = $customer->getByEmail($email);

			if(!$authentication OR !$customer->id){
				$_POST['email'] = $email;
				$_POST['passwd'] = $passwd;
				$_POST['firstname'] = $data['first_name'];
				$_POST['lastname'] = $data['last_name'];
				if($data['sex'] == 1){
					$_POST['id_gender'] = 2;
				}
				elseif($data['sex'] == 2){
					$_POST['id_gender'] = 1;
				}

				if(!empty($data['bdate'])){
					$bdate = explode(".", $data['bdate']);
					$bd    = date('Y-m-d H:i:s', mktime(0,0,0,$bdate[1],$bdate[0], $bdate[2]));

					if(Validate::isBirthDate($bd))								$customer->birthday = $bd;
				}




				$errors = $customer->validateControler();
				if(!sizeof($errors)){
					$customer->active = 1;
					if(!$customer->add())
					{
						$errors[] = Tools::displayError('an error occurred while creating your account');
					}
					else
					{
						Mail::Send(
							$cookie->id_lang,
							'account',
							Mail::l('Welcome!'),
							array(
								'{firstname}'=> $customer->firstname,
								'{lastname}' => $customer->lastname,
								'{email}'    => $customer->email,
								'{passwd}'   => Tools::getValue('passwd')),
							$customer->email,
							$customer->firstname.' '.$customer->lastname
						);
					}
					if(Configuration::get('_ADR_'))				Db::getInstance()->Execute("
						INSERT INTO `"._DB_PREFIX_."address`
						(`id_country`, `id_state`, `id_customer`, `alias`,`lastname`,`firstname`,`address1`,`postcode`,`city`,`phone`,`phone_mobile`,`date_add`,`date_upd`)
						VALUES
						(8, 1 , ".intval($customer->id)." , 'JMERINKA', '".$customer->lastname."', '".$customer->firstname."','Moskow','12345','Тамбов','569880','+79999999999', NOW(), NOW())
						");
				}

			}


			if($customer->customerExists($email))
			{

				$cookie->id_customer = intval($customer->id);
				$cookie->customer_lastname = $customer->lastname;
				$cookie->customer_firstname = $customer->firstname;
				$cookie->logged = 1;
				$cookie->passwd = $customer->passwd;
				$cookie->email = $customer->email;
				$cookie->is_guest = 0;

				$cart->secure_key = $customer->secure_key;
				$cart->update();
				$qty = $cart->nbProducts();

					if($back = Tools::getValue('back'))
					{
						Tools::redirect($back);
					}
					switch(Configuration::get('_PAGE_'))
					{

						case 2:
						if($qty > 0){
							Tools::redirect('order.php');
						}
						else
						{
							Tools::redirect('my-account.php');
						}
						break;
						case 1:
						Tools::redirect('my-account.php');
						break;
					}

				}
		}



	}
	public
	function Uauth()
	{
		$this->ulogin();
	}

}
