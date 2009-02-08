<?php

/**
 * BaseRelease
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $title
 * @property integer $artistId
 * @property boolean $published
 * @property timestamp $publishDate
 * @property Doctrine_Collection $Track
 * @property Artist $Artist
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5318 2008-12-19 20:44:54Z jwage $
 */
abstract class BaseRelease extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('releases');
    $this->hasColumn('title', 'string', 64, array('notblank' => true, 'type' => 'string', 'length' => '64'));
    $this->hasColumn('artistId', 'integer', null, array('notnull' => true, 'type' => 'integer'));
    $this->hasColumn('published', 'boolean', null, array('type' => 'boolean', 'notnull' => true, 'default' => true));
    $this->hasColumn('publishDate', 'timestamp', null, array('type' => 'timestamp', 'notnull' => true));
  }

  public function setUp()
  {
    $this->hasMany('Track', array('local' => 'id',
                                  'foreign' => 'releaseId'));

    $this->hasOne('Artist', array('local' => 'artistId',
                                  'foreign' => 'id'));

    $timestampable0 = new Doctrine_Template_Timestampable();
    $sluggable0 = new Doctrine_Template_Sluggable(array('unique' => true, 'fields' => array(0 => 'title', 1 => 'id'), 'uniqueBy' => array(0 => 'title', 1 => 'id'), 'canUpdate' => true));
    $this->actAs($timestampable0);
    $this->actAs($sluggable0);
  }
}