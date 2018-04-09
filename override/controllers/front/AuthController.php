<?php
class AuthController extends AuthControllerCore
{
	/*
    * module: ulogin
    * date: 2018-04-09 13:44:20
    * version: 0.8
    */
    public function preProcess()
	{
		parent::preProcess();
		self::$smarty->assign(array(
			'HOOK_AUTH' => Module::hookExec('auth')
		));
	}
}
