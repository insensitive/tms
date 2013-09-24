<?php
namespace AfcCommons\StaticOptions;

use AfcCommons\StaticOptions\Entity as EntityOptions;

class StaticOptions
{

    /**
     * Store Instance of Service Locator
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    private static $_serviceLocator;

    /**
     * Current User instance
     *
     * @var \AfcCommons\Entity\User
     */
    private static $_user;

    /**
     * Instance of doctrine entity manager
     *
     * @var $_em | Doctrine Entity Manager
     */
    private static $_em;

    /**
     * Mysql DateTime Format
     *
     * @var String | Format
     */
    private static $MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";

    /**
     * Mysql Date Format
     *
     * @var String | Format
     */
    private static $MYSQL_DATE_FORMAT = "Y-m-d";

    /**
     * Store default access mode for user
     * This is used for DB row level ACL
     *
     * @var enum(0,1,2,3,4,5,6,7)
     */
    private static $_default_user_access_mode = 7;

    /**
     * Store default access mode for group of users
     * This is used for DB row level ACL
     *
     * @var enum(0,1,2,3,4,5,6,7)
     */
    private static $_default_group_access_mode = 1;

    /**
     * Store default access mode for other users
     * This is used for DB row level ACL
     *
     * @var enum(0,1,2,3,4,5,6,7)
     */
    private static $_default_other_access_mode = 0;

    private static $_cache_controller;

    /**
     * Store the base url once initialized
     *
     * @var String $_base_url
     */
    private static $_base_url = null;

    /**
     * Store the guest user entity
     *
     * @var \AfcCommons\Entity\User
     */
    private static $_guest_user;

    /**
     * Store whether DB row level acl is used or enabled or not
     *
     * @var boolean
     */
    private static $_is_db_row_level_acl_enabled = false;

    /**
     * Get the base url of the application
     */
    public static function getBaseUrl ()
    {
        if (static::$_base_url == null) {
            static::$_base_url = self::getServiceLocator()->get('application')
                ->getRequest()
                ->getBasePath();
        }
        return static::$_base_url;
    }

    /**
     * Return the service locator
     */
    public static function getServiceLocator ()
    {
        return static::$_serviceLocator;
    }

    /**
     * Set the service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator            
     */
    public static function setServiceLocator (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        static::$_serviceLocator = $serviceLocator;
    }

    /**
     * Get doctrine metadata from classname
     *
     * @param string $className            
     * @throws \Exception
     */
    public static function getClassMetaData ($className)
    {
        if ($className == null) {
            throw new \Exception("Class name cannot be null, for getting metadata");
        }
        return static::getEntityManager()->getClassMetaData($className);
    }

    /**
     * Get the doctrine entity manager
     */
    public static function getEntityManager ()
    {
        if (static::$_em == null) {
            static::$_em = static::getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return static::$_em;
    }

    /**
     * Get the user model
     */
    public static function getCurrentUser ()
    {
        if (static::$_user == null) {
            static::setCurrentUser(static::getGuestUserObject());
        }
        return static::$_user;
    }

    /**
     * Set the user model
     *
     * @param Entity $user            
     */
    public static function setCurrentUser ($user)
    {
        static::$_user = $user;
    }

    /**
     * Return the guest user object
     *
     * @return \AfcCommons\Entity\User $user
     */
    public static function getGuestUserObject ()
    {
        if (static::$_guest_user == null) {
            $identtityClass = self::getConfig('doctrine:authenticationadapter:odm_default:identityClass');
            $userClass = $identtityClass;
            $guestUser = self::getEntityManager()->getRepository($userClass)->findBy(array(
                "user_id" => 1
            ));
            $guestUser = array_pop($guestUser);
            
            if (! $guestUser instanceof \AfcCommons\Entity\User) {
                throw new \Exception("{$userClass} should be an instance of AfcCommons\Entity\User");
            }
            
            static::$_guest_user = $guestUser;
        }
        
        return static::$_guest_user;
    }

    /**
     * A shortcut method to get the configuration from config with seperator as
     * ":"
     * for example if there is some thing like
     * invokables(array)->controllers(array)->data(array or end result)
     * then getConfig(invokables:controllers:abcd); would fetch
     * the end value of data
     *
     * @param string $configPath            
     * @throws \Exception
     * @return Ambigous <unknown, object, multitype:>
     */
    public static function getConfig ($configPath = "")
    {
        $config = self::getServiceLocator()->get('Config');
        if ($configPath == "") {
            return $config;
        }
        $arrayPath = explode(":", $configPath);
        $currentLocation = $config;
        foreach ($arrayPath as $path) {
            if (isset($currentLocation[$path])) {
                $currentLocation = $currentLocation[$path];
            } else {
                throw new \Exception("Path not found: " . $configPath);
            }
        }
        return $currentLocation;
    }

    /**
     * Search with values in an array recursively
     *
     * @param
     *            $needle
     * @param
     *            $haystack
     * @return $value boolean
     */
    public static function recursive_array_search ($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && self::recursive_array_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    /**
     * Set the DB row level acl value (Enabled or not)
     *
     * @param string $boolean            
     */
    public static function setDbRowLevelAclEnabled ($boolean = false)
    {
        static::$_is_db_row_level_acl_enabled = $boolean;
    }

    /**
     * Return true if DB row level ACL is enabled
     *
     * @return boolean
     */
    public static function isDbRowLevelAclEnabled ()
    {
        return static::$_is_db_row_level_acl_enabled;
    }

    /**
     * Set the user's default access mode
     *
     * @param enum(0,1,2,3,4,5,6,7) $userAccessMode            
     */
    public static function setDefaultUserAccessMode ($userAccessMode)
    {
        static::$_default_user_access_mode = $userAccessMode;
    }

    /**
     * Get the user's default access mode
     *
     * @return enum(0,1,2,3,4,5,6,7) $userAccessMode
     */
    public static function getDefaultUserAccessMode ()
    {
        return static::$_default_user_access_mode;
    }

    /**
     * Set the user's group default access mode
     *
     * @param enum(0,1,2,3,4,5,6,7) $groupAccessMode            
     */
    public static function setDefaultGroupAccessMode ($groupAccessMode)
    {
        static::$_default_group_access_mode = $groupAccessMode;
    }

    /**
     * Get the user's group default access mode
     *
     * @return enum(0,1,2,3,4,5,6,7) $groupAccessMode
     */
    public static function getDefaultGroupAccessMode ()
    {
        return static::$_default_group_access_mode;
    }

    /**
     * Set the other user's default access mode
     *
     * @param enum(0,1,2,3,4,5,6,7) $otherAccessMode            
     */
    public static function setDefaultOtherAccessMode ($otherAccessMode)
    {
        static::$_default_other_access_mode = $otherAccessMode;
    }

    /**
     * get the other user's default access mode
     *
     * @return enum(0,1,2,3,4,5,6,7) $otherAccessMode
     */
    public static function getDefaultOtherAccessMode ()
    {
        return static::$_default_group_access_mode;
    }

    /**
     * check if current user has write access
     *
     * @param string $className            
     * @param number $primary_key            
     * @return boolean
     */
    public static function hasWriteAccess ($className = null, $primary_key = 0)
    {
        if ($className == null) {
            return false;
        }
        $recordAccessMode = static::getRecordAccessMode(static::getCurrentUser(), $className, $primary_key, "write");
        return EntityOptions::checkWriteAccess($recordAccessMode);
    }

    /**
     * check if the current user has rights to delete
     *
     * @param string $className            
     * @param number $primary_key            
     * @return boolean
     */
    public static function hasDeleteAccess ($className = null, $primary_key = 0)
    {
        if ($className == null) {
            return false;
        }
        $recordAccessMode = static::getRecordAccessMode(static::getCurrentUser(), $className, $primary_key, "delete");
        return EntityOptions::checkDeleteAccess($recordAccessMode);
    }

    /**
     *
     * @param \AfcCommons\Entity\User $user            
     * @param string $className            
     * @throws \Exception
     * @return number
     */
    public static function getRecordAccessMode (\AfcCommons\Entity\User $user, $className, $primaryKey)
    {
        $em = static::getEntityManager();
        
        $recordAccessArray = $em->getRepository('AfcCommons\Entity\RecordAccess')->findBy(array(
            'primary_key' => $primaryKey,
            'primary_key_column' => static::getClassMetaData($className)->getSingleIdentifierColumnName(),
            'table_name' => static::getClassMetaData($className)->getTableName()
        ));
        $recordAccessDetails = array_pop($recordAccessArray);
        if ($recordAccessDetails == null) {
            $authorizePreviousDbRecords = self::getConfig("acl:options:authorize_previous_db_records");
            if($authorizePreviousDbRecords){
                return 7;
            } else {
                return 0;
            }
        }
        
        if ($recordAccessDetails->getUserId() == $user->getUserId()) {
            return $recordAccessDetails->getUserAccessMode();
        }
        try {
            $userClass = static::getConfig("doctrine:authenticationadapter:odm_default:identityClass");
        } catch (\Exception $ex) {
            throw new \Exception("Identity class for doctrine authentication adapter not found", 500);
        }
        
        $otherUser = $em->getRepository($userClass)->find($recordAccessDetails->getUserId());
        if (! $otherUser) {
            $otherUser = self::getGuestUserObject();
        }
        
        if ($otherUser->getGroup()->getGroupId() == $user->getGroup()->getGroupId()) {
            return $recordAccessDetails->getGroupAccessMode();
        }
        return $recordAccessDetails->getOtherAccessMode();
    }

    /**
     * Send 403 Unauthorized response
     *
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public static function get403Response ($error_code = "acl", $send = false)
    {
        if ($error_code == "db-acl") {
            $error_code = "error-db-row-level-acl";
        } else {
            $error_code = "error-acl";
        }
        // Get the initialized view helper manager from the service manager
        $viewHelperManager = self::getServiceLocator()->get('ViewHelperManager');
        
        // Get the template map from configurations
        $templateMap = self::getConfig("view_manager:template_map");
        
        // Generate a custom response
        $response = new \Zend\Http\PhpEnvironment\Response();
        $response->setStatusCode(403);
        
        // Generate an instance of view model to be rendered
        $model = new \Zend\View\Model\ViewModel();
        $model->setTemplate("layout/layout");
        
        $contentModel = new \Zend\View\Model\ViewModel();
        $contentModel->setTemplate('error/403');
        $contentModel->setVariables(array(
            'message' => "Forbidden",
            "reason" => (string) $error_code
        ));
        
        // Initialize the template map resolver
        $resolver = new \Zend\View\Resolver\TemplateMapResolver($templateMap);
        
        // Initialize the renderer
        $renderer = new \Zend\View\Renderer\PhpRenderer();
        
        // Set the Helper manager of the renderer with the helper initialized
        // with service locator
        $renderer->setHelperPluginManager($viewHelperManager);
        
        $renderer->setResolver($resolver);
        
        $contentOfModel = $renderer->render($contentModel);
        $model->setVariable("content", $contentOfModel);
        
        $content = $renderer->render($model);
        
        $response->setContent($content);
        if ($send) {
            $response->send();
            die();
        }
        return $response;
    }
    /**
     * Send 302 Permanant redirect response
     *
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public static function get302Response ($redirectUrl = "/")
    {
        $response = new \Zend\Http\PhpEnvironment\Response();
        $response->getHeaders()->addHeaderLine('Location', self::getBaseUrl() . $redirectUrl);
        $response->send();
        exit();
    }
}