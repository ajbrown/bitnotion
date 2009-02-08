<?php

/**
 * BaseAccountLogin
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $accountId
 * @property integer $ip
 * @property Account $Account
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5318 2008-12-19 20:44:54Z jwage $
 */
abstract class BaseAccountLogin extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('accounts_logins');
    $this->hasColumn('accountId', 'integer', null, array('type' => 'integer', 'notnull' => true));
    $this->hasColumn('ip', 'integer', 4, array('type' => 'integer', 'length' => '4'));
  }

  public function setUp()
  {
    $this->hasOne('Account', array('local' => 'accountId',
                                   'foreign' => 'id'));

    $timestampable0 = new Doctrine_Template_Timestampable();
    $this->actAs($timestampable0);
  }
}