<?php

require_once 'App/Table/Abstract.php';

class App_Table_Release extends App_Table_Abstract
{

    /**
     * Finds all published releases for the specified artist ide
     *
     * @param integer $artistId
     * @return Doctrine_Collection
     */
    public function findPublishedByArtist( $artistId )
    {

        $query = $this->createQuery()
            ->where( 'artistId = ?', array( $artistId ) )
            ->addWhere( 'published' )
            ->addWhere( 'publishDate <= NOW()' )
            ->orderBy( 'publishDate DESC' )
            ;

        return $query->execute();
    }
}