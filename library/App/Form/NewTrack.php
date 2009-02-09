<?php

require_once  'Zend/Dojo/Form.php';

class App_Form_NewTrack extends Zend_Dojo_Form
{

	public function init()
	{
		parent::init();

		$config = Initializer::getConfig();
		$userId = Zend_Auth::getInstance()->getIdentity()->id;

		$this->setAttrib( 'id', 'new-track-form' );

		$this->setDecorators( array(
			'FormElements',
			'DijitForm',
		) );

		$this->addElement( 'FilteringSelect', 'releaseId', array(
            'label'		    => 'Release',
			'storeId'	    => 'artistReleasesDataStore',
			'storeType'	    => 'dojo.data.ItemFileReadStore',
		    'description'	=> 'The release to attach this track to.  The track will still be downloadable as a single track, but will be grouped under the release',
            'autocomplete'  => true,
			'storeParams'   => array(
				'url' => '/data/artists/releases/' . $userId . '?published=0',
		        'requestMethod' => 'get'
		    ),
			'dijitParams'   => array( 'searchAttr' => 'title' ),
        ) );

		$this->addElement( 'ValidationTextBox', 'title', array(
				'label'		=> 'Track Title',
				'required' 	=> true,
				'trim'		=> true,
		        'description' => 'The display title, appears in store',
				'validators'  => array(
					array( 'Alnum', false, array( 'allowWhiteSpace' => true ) )
				)
	    ) );

		$this->addElement( 'DateTextBox', 'publishDate', array(
				'label'		=> 'Publish Date',
		        'description' => 'The date which this track will be available for purchase',
		        'default'	=> date( 'm/d/Y' ),
		) );

		$audioFile = new Zend_Form_Element_File( 'audioFile' );
		$audioFile->setLabel( 'Master Audio File' )
		    ->addDecorators( array( 'Description' ) )
		    ->setRequired( true )
		    ->setDescription( 'The original (master) track.  Must be a .WAV file' )
		    ->setDestination( $config->injestion->workDir )
		    ->addValidator( 'Extension', false, explode( ',', $config->injestion->allowedExtensions ) )
		    ->addValidator(	'MimeType', false, explode( ',', $config->injestion->allowedMimeTypes ) )
		    ->addFilter('Rename', array(
		    	'target' => $config->injestion->workDir . '/track_'. str_pad( $userId, 8, '0' ).'_'.date('Ymdhs').'.wav',
				'overwrite' => true )
		    )
		    ;

		$this->addElement( $audioFile );
		$this->setEnctype( 'multipart/form-data' );


		$this->addElement( 'SubmitButton', 'submit',
			array(
				'ignore'	=> true,
				'label'		=> 'Upload Track'
			)
		);

	}

}