<?php

class wn_site_commentsCommentsModuleFrontController extends ModuleFrontController
{
	
	public function initContent()
	{
		parent::initContent();
		
		$wn_site_comments = new wn_site_comments;
		
		
		
		
		

		$this->setTemplate('comments.tpl');
	}
	
	
}