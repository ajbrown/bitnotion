<?php
require_once 'BaseController.php';

/**
 * AccountController
 *
 * @author
 * @version
 */

class Dashboard_CatalogController extends Dashboard_BaseController
{

    public function indexAction()
    {

    }

    public function addreleaseAction()
    {


    }

    public function addtrackAction()
    {

        $form = new App_Form_NewTrack();

        if ( !empty( $_POST ) && $form->isValid( $_POST ) ) {

            //--------------------------------
            // Check the file info, and save
            //--------------------------------
            $track = new Track();
            $track->title        = $form->getValue( 'title' );
            $track->artistId     = $this->_identity->id;
            $track->releaseId    = $form->getValue( 'releaseId' );
            $track->single       = true;
            $track->publishDate  = $form->getValue( 'publishDate' );
            $track->save();

            $this->_flash->addMessage( 'Your track info has been saved.' );

            $file = new TrackFile();
            $file->trackId    = $track->id;
            $file->fileName   = $form->audioFile->getFileName();
            $file->mimeType   = mime_content_type( $file->fileName );
            $file->save();

            $track->originalFileId = $file->id;
            $track->save();

            $this->_flash->addMessage( 'The audio file for your track has been scheduled for injestion.  Once the file has been injested, it will be available on the site.' );

            $form = new App_Form_NewTrack();
        }

        $form->setMethod( 'post' );
        $form->setAction( '/dashboard/catalog/addtrack' );
        $this->view->form = $form;
    }
}