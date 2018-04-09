<?php
class Init{
	public $prov = array('vkontakte', 'odnoklassniki', 'mailru', 'facebook', 'twitter', 'google', 'yandex', 'livejournal', 'lastfm', 'linkedin', 'liveid', 'soundcloud','steam', 'tumblr', 'flickr', 'vimeo', 'youtube','webmoney', 'foursquare', 'googleplus', 'dudu', 'openid');
	private $ulogin; 
	public function __construct(){
     
        $this->ulogin = new UloginAuth();
  } 
	function run()
	{
		$this->ulogin->Uauth();
	}
		
}