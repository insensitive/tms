<?php

/**
 * File for Event Class
 *
 * @category  User
 * @package   User_Event
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */

/**
 *
 * @namespace AfcCommons\Permissons\Event
 */
namespace AfcCommons\Permissions\Event;

/**
 *
 * @uses Zend\Mvc\MvcEvent
 * @uses User\Controller\Plugin\UserAuthentication
 * @uses User\Acl\Acl
 */
use Zend\Mvc\MvcEvent as MvcEvent;
use AfcCommons\Permissions\Authentication\Authentication as AuthPlugin;
use AfcCommons\Permissions\Acl\Acl as AclClass;
use AfcCommons\StaticOptions\StaticOptions as StaticOptions;

/**
 * Authentication Event Handler Class
 *
 * This Event Handles Authentication
 *
 * @category User
 * @package User_Event
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license http://binware.org/license/index/type:new-bsd New BSD License
 */
class Authentication
{

    /**
     *
     * @var AuthPlugin
     */
    protected $_userAuth = null;

    /**
     *
     * @var AclClass
     */
    protected $_aclClass = null;

    /**
     *
     * @var \Zend\Mvc\MvcEvent $event
     */
    protected $_event = null;

    /**
     * Store all the groups
     *
     * @var $_groups Array
     */
    protected $_groups = array();

    /**
     * Store the current role of user
     *
     * @var $_currentRole Default: GUEST
     */
    protected $_currentRole = "GUEST";

    /**
     * Store the current user id
     *
     * @var $_currentUserId Default: 0
     */
    protected $_currentUserId = 0;

    protected $_redirect403 = array();

    /**
     * preDispatch Event Handler
     *
     * @param \Zend\Mvc\MvcEvent $event            
     * @throws \Exception
     */
    public function preDispatch (MvcEvent $event)
    {
        $userAuth = $this->getUserAuthenticationPlugin();
        $this->_event = $event;
        
        // Initialize AclClass groups
        try {
            $groups = $this->getEntityManager()
                ->getRepository('AfcCommons\Entity\Group')
                ->findAll();
        } catch (\Exception $ex) {
            throw new \Zend\Http\Exception\RuntimeException("Please create the database tables needed for AfcCommons to 
                operate. you can find the sql schema in AfcCommons/data/create_schema.sql", 500, $ex);
        }
        foreach ($groups as $group) {
            $this->_groups[$group->getGroupId()] = $group->getName();
        }
        // Add the groups to ACL class
        AclClass::$groups = $this->_groups;
        
        // Get default role
        $role = AclClass::DEFAULT_ROLE;
        
        if ($userAuth->hasIdentity()) {
            $user = $userAuth->getIdentity();
        } else {
            $user = StaticOptions::getGuestUserObject();
        }
        $role = $user->getGroup()->getName();
        $this->_currentRole = $role;
        $this->_currentUserId = $user->getUserId();
        
        // Get the ACL class
        $acl = $this->getAclClass();
        
        $routeMatch = $event->getRouteMatch();
        
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        
        $redirectUrl = false;
        $aclAction = "allow";
        if (! $acl->hasResource($controller)) {
            try {
                $aclAction = strtolower(StaticOptions::getConfig("acl:options:access_when_no_resource_found"));
            } catch (\Exception $ex) {
                $aclAction = "allow";
            }
        } else {
            if ($acl->isAllowed($role, $controller, $action)) {
                $aclAction = "allow";
            } else {
                $aclAction = "deny";
            }
        }
        if ($aclAction == "deny") {
            $searchKey = StaticOptions::recursive_array_search($controller, $this->_redirect403);
            if ($searchKey !== false && $action == $this->_redirect403[$searchKey]["action"]) {
                $redirectArray = $this->_redirect403[$searchKey];
                if (isset($redirectArray['redirect-group']) && isset($redirectArray['redirect-group'][$this->_currentRole])) {
                    $redirectUrl = $redirectArray['redirect-group'][$this->_currentRole];
                }
            }
            if ($redirectUrl === false) {
                return StaticOptions::get403Response();
            } else {
                return StaticOptions::get302Response($redirectUrl);
            }
        }
    }

    /**
     * Sets Authentication Plugin
     *
     * @param \User\Controller\Plugin\UserAuthentication $userAuthenticationPlugin            
     * @return Authentication
     */
    public function setUserAuthenticationPlugin (AuthPlugin $userAuthenticationPlugin)
    {
        $this->_userAuth = $userAuthenticationPlugin;
        
        return $this;
    }

    /**
     * Gets Authentication Plugin
     *
     * @return \User\Controller\Plugin\UserAuthentication
     */
    public function getUserAuthenticationPlugin ()
    {
        if ($this->_userAuth === null) {
            $this->_userAuth = new AuthPlugin();
        }
        
        return $this->_userAuth;
    }

    /**
     * Sets ACL Class
     *
     * @param \User\Acl\Acl $aclClass            
     * @return Authentication
     */
    public function setAclClass (AclClass $aclClass)
    {
        $this->_aclClass = $aclClass;
        
        return $this;
    }

    /**
     * Gets ACL Class
     *
     * @return \User\Acl\Acl
     */
    public function getAclClass ()
    {
        if ($this->_aclClass === null) {
            $aclArray = $this->getAclConfig();
            $this->_aclClass = new AclClass($aclArray);
        }
        
        return $this->_aclClass;
    }

    public function getAclConfig ()
    {
        $serviceLocator = $this->getServiceLocator();
        
        // Initializing Roles
        $roles = array();
        
        $roles[$this->_currentRole] = null;
        
        $roles[$this->_currentUserId] = null;
        
        // Get current account type id
        $current_group_id = array_search($this->_currentRole, $this->_groups);
        
        $current_user_id = $this->_currentUserId;
        $resources = array();
        
        $query = $this->getEntityManager()->getConnection();
        $sql = "SELECT afc_mdb.*,afc_group_acl.access as access FROM afc_mdb,afc_group_acl where afc_mdb.afc_mcategory_id IN (SELECT afc_mcategory_id FROM afc_group_acl WHERE afc_group_id = '" . $current_group_id;
        $sql .= "' UNION SELECT afc_mcategory_id FROM afc_user_acl WHERE user_id = '" . $current_user_id . "') AND afc_mdb.afc_mcategory_id = afc_group_acl.afc_mcategory_id";
        $mdbs = $query->fetchAll($sql);
        
        // Generate the resources as by the current user's acl
        foreach ($mdbs as $mdb) {
            $resourceName = $mdb['controller'];
            $access = strtolower($mdb['access']);
            $action = $mdb['action'];
            
            // Add allow action
            if (! isset($resources[$access])) {
                $resources[$access] = array();
            }
            if (! isset($resources[$access][$resourceName])) {
                $resources[$access][$resourceName] = array();
            }
            if (! isset($resources[$access][$resourceName][$action])) {
                $resources[$access][$resourceName][$action] = "";
            }
            
            $resources[$access][$resourceName][$action] = array_keys($roles);
        }
        // Get the guards(from acl configurations)
        try {
            $guard = StaticOptions::getConfig("acl:guard");
        } catch (\Exception $ex) {
            $guard = array();
        }
        
        try {
            $this->_redirect403 = StaticOptions::getConfig("acl:403-Redirect");
        } catch (\Exception $ex) {
            $this->_redirect403 = array();
        }
        $this->refine403Redirect();
        
        foreach ($guard as $resourceName => $actionArray) {
            if (is_array($actionArray) && ! empty($actionArray)) {
                $action = array_keys($actionArray);
                $action = array_pop($action);
                if ($action != null) {
                    if (in_array($this->_currentRole, $actionArray[$action]['groups'])) {
                        $access = strtolower("ALLOW");
                        if (! isset($resources[$access])) {
                            $resources[$access] = array();
                        }
                        if (! isset($resources[$access][$resourceName])) {
                            $resources[$access][$resourceName] = array();
                        }
                        if (! isset($resources[$access][$resourceName][$action])) {
                            $resources[$access][$resourceName][$action] = "";
                        }
                        $resources[$access][$resourceName][$action] = array_keys($roles);
                    }
                }
            }
        }
        
        $aclConfig = array(
            'acl' => array(
                'roles' => $roles,
                'resources' => $resources
            )
        );
        AclClass::$config = $aclConfig;
        AclClass::$groups = $this->_groups;
        return $aclConfig;
    }

    public function getCurrentRole ()
    {
        return $this->_currentRole;
    }

    private function getEntityManager ()
    {
        if ($this->_event == null)
            throw new \Exception("Event variable not initialized");
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    private function getServiceLocator ()
    {
        if ($this->_event == null)
            throw new \Exception("Event variable not initialized");
        return $this->_event->getTarget()->getServiceLocator();
    }
    private function refine403Redirect(){
        $redirect403 = array();
        foreach($this->_redirect403 as $redirect){
            if(StaticOptions::recursive_array_search($redirect['resource'], $redirect403)===false){
                array_push($redirect403,$redirect);
            } else if(($key = StaticOptions::recursive_array_search($redirect['action'], $redirect403))===false){
                array_push($redirect403,$redirect);
            } else {
                foreach($redirect['redirect-group'] as $groupName =>  $redirectValue){
                    $redirect403[$key]['redirect-group'][$groupName] = $redirectValue;
                }
            }
        }
        $this->_redirect403 = $redirect403;
    }
}