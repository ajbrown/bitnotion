<?php

require_once  'Zend/Dojo/Form.php';

class App_Form_Login extends Zend_Dojo_Form
{

	public function init()
	{
		parent::init();

		$this->setName( 'loginForm' );
		$this->setAttrib( 'id', 'release-form' );
		$this->setMethod( 'post' );

		$this->setDecorators( array(
			'FormElements',
			'DijitForm',
		) );


		$this->addElement( 'ValidationTextBox', 'title',
			array(
				'label'		=> 'Username',
				'required' 	=> true,
				'trim'		=> true,
			    'validators'	=> array(
			        array( 'Alnum', true, array( 'allowWhiteSpace' => true ) )
			    )
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