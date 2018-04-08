<?php

class AuthController extends AuthControllerCore
{
	public function preProcess()
	{
		parent::preProcess();
		self::$smarty->assign(array(
			'HOOK_AUTH' => Module::hookExec('auth')
		));
	}
}


