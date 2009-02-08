<?php

require_once  'Zend/Dojo/Form.php';

class App_Form_Login extends Zend_Dojo_Form
{

	public function init()
	{
		parent::init();

		$this->setName( 'loginForm' );
		$this->setAttrib( 'id', 'login-form' );
		$this->setAction( '/account/login' );
		$this->setMethod( 'post' );

		$this->setDecorators( array(
			'FormElements',
			'DijitForm',
		) );


		$this->addElement( 'ValidationTextBox', 'username',
			array(
				'label'		=> 'Username',
				'required' 	=> true,
				'trim'		=> true,
				'filters'  => array( 'StringToLower' )
			)
		);

		$this->addElement( 'PasswordTextBox', 'password',
			array(
				'label'		=> 'Password',
				'required'	=> true,
				'trim'		=> false
			)
		);

		$this->addElement( 'SubmitButton', 'submit',
			array(
				'ignore'	=> true,
				'label'		=> 'Log Me In'
			)
		);

	}

}