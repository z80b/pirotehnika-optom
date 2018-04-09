<?php
class AuthController extends AuthControllerCore
{
	/*
    * module: ulogin
    * date: 2018-04-07 01:01:04
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
