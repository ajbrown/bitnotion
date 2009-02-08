<?php

require_once ( 'Zend/Controller/Action.php' );

class ZForge_Controller_Action extends Zend_Controller_Action
{

	/**
	 * Enter description here...
	 *
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	protected $_flash;

	/**
	 * Enter description here...
	 *
	 * @var Zend_Controller_Action_Helper_Redirector
	 */
	protected $_redirector;

	protected $_identity;

	/**
	 * @var Zend_Config_Xml
	 */
	protected $_config;

	public function init()
	{
		parent::init();

		$this->_flash 		= $this->_helper->getHelper( 'FlashMessenger' );
		$this->_redirector 	= $this->_helper->getHelper( 'Redirector' );

		$this->checkAuth();

		$this->_identity = Zend_Auth::getInstance()->getIdentity();

		Zend_Dojo::enableView( $this->view );

		//TODO there's gotta be a better way to do this
		$this->_config = Zend_Registry::get( 'Config' );
	}

	public function preDispatch()
	{
	    $this->view->messages = $this->_flash->getMessages();
	    $this->view->identity = Zend_Auth::getInstance()->getIdentity();
	}


	public function isPublic()
	{
	    return true;
	}

	public function checkAuth()
	{
		if( !$this->isPublic() && !Zend_Auth::getInstance()->hasIdentity() ) {
		    $this->_flash->addMessage( 'You must be logged in' );
		    $this->_redirector->gotoSimple( 'login', 'account' );
		}
	}

}