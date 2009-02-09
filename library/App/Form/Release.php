<?php

require_once  'Zend/Dojo/Form.php';

class App_Form_Release extends Zend_Dojo_Form
{

	public function init()
	{
		parent::init();

		$this->setName( 'releaseForm' );
		$this->setAttrib( 'id', 'release-form' );
		$this->setMethod( 'post' );

		$this->setDecorators( array(
			'FormElements',
			'DijitForm',
		) );

		$this->addElement( 'ValidationTextBox', 'title',
			array(
				'label'		=> 'Release Title',
				'required' 	=> true,
				'trim'		=> true,
			    'validators'	=> array(
			        array( 'Alnum', true, array( 'allowWhiteSpace' => true ) )
			    ),
			)
		);

		$this->addElement( 'DateTextBox', 'publishDate', array(
				'label'		=> 'Publish Date',
		        'description' => 'The date which this release will be available
		        	for purchase.  Note: tracks may be available as singles
		        	before this date.',
		        'default'	=> date( 'm/d/Y' ),
		) );

		$this->addElement( 'SubmitButton', 'submit',
			array(
				'ignore'	=> true,
				'label'		=> 'Save Release'
			)
		);

	}

}