<?php


abstract class Dashboard_BaseController extends ZForge_Controller_Action
{

    public function init()
    {
        parent::init();

    }

	public function checkAuth()
	{
		if( !Zend_Auth::getInstance()->hasIdentity()
		    || !Zend_Auth::getInstance()->getIdentity()->type == Account::TYPE_SELLER ) {
		    $this->_flash->addMessage( 'You must be logged in' );
		    $this->_redirector->gotoSimple( 'login', 'account', 'default' );
		}
	}
}