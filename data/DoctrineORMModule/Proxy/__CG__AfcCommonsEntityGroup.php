<?php

namespace DoctrineORMModule\Proxy\__CG__\AfcCommons\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Group extends \AfcCommons\Entity\Group implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function get($var)
    {
        $this->__load();
        return parent::get($var);
    }

    public function set($var, $value)
    {
        $this->__load();
        return parent::set($var, $value);
    }

    public function exchangeArray(array $options)
    {
        $this->__load();
        return parent::exchangeArray($options);
    }

    public function hasVariable($variableName)
    {
        $this->__load();
        return parent::hasVariable($variableName);
    }

    public function getServiceLocator()
    {
        $this->__load();
        return parent::getServiceLocator();
    }

    public function getEntityManager()
    {
        $this->__load();
        return parent::getEntityManager();
    }

    public function getGridSql(array $gridInitialData = array (
))
    {
        $this->__load();
        return parent::getGridSql($gridInitialData);
    }

    public function getFilteredRecordSql(array $gridInitialData = array (
))
    {
        $this->__load();
        return parent::getFilteredRecordSql($gridInitialData);
    }

    public function getTotalRecordSql()
    {
        $this->__load();
        return parent::getTotalRecordSql();
    }

    public function getGridData(\Zend\Http\PhpEnvironment\Request $request, array $options = array (
))
    {
        $this->__load();
        return parent::getGridData($request, $options);
    }

    public function getIntialGridConditions(\Zend\Http\PhpEnvironment\Request $request, array $options = array (
))
    {
        $this->__load();
        return parent::getIntialGridConditions($request, $options);
    }

    public function filterGridResult()
    {
        $this->__load();
        return parent::filterGridResult();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'group_id', 'parent_group_id', 'name', 'users');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}