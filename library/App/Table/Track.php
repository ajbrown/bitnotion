<?php

require_once 'App/Table/Abstract.php';

class App_Table_Track extends App_Table_Abstract
{

    /**
     * Finds all published releases for the specified artist ide
     *
     * @param integer $artistId
     * @param boolean $useCache use the result cache? This should almost
     * 	always be true!
     * @return Doctrine_Collection
     */
    public function findPublishedByArtist( $artistId )
    {
        $query = $this->createQuery()
            ->where( 'artistId = ?', array( $artistId ) )
            ->addWhere( 'published' )
            ->addWhere( 'enabled' )
            ->addWhere( 'encodingStatus = ?', array( 'COMPLETE' ) )
            ->addWhere( 'publishDate <= NOW()' )
            ->orderBy( 'publishDate DESC' )
            ;

        return $query->execute();
    }

    public function findPublishedByRelease( $releaseId )
    {
        $query = $this->createQuery()
            ->where( 'releaseId = ?', array( $releaseId ) )
            ->addWhere( 'published' )
            ->addWhere( 'enabled' )
            ->addWhere( 'encodingStatus = ?', array( 'COMPLETE' ) )
            ->addWhere( 'publishDate <= NOW()' )
            ->orderBy( 'publishDate DESC' )
            ;
    }
}