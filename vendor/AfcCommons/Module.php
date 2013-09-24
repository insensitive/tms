<?php
namespace AfcCommons;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\EventManager\StaticEventManager;
use AfcCommons\StaticOptions\StaticOptions;

class Module implements AutoloaderProviderInterface
{

    /**
     * Store the current namespace in this variable
     *
     * @var current namespace
     */
    protected $_namespace = __NAMESPACE__;

    /**
     * Store the current directory
     *
     * @var current directory
     */
    protected $_dir = __DIR__;

    public function onBootstrap ($e)
    {
        $e->getApplication()
            ->getEventManager()
            ->getSharedManager()
            ->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function  ($e)
        {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config = $e->getApplication()
                ->getServiceManager()
                ->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
    }

    public function init ()
    {
        // Execute the events only once when the Afc Module is loaded
        if ($this->_namespace == __NAMESPACE__) {
            
            // Get the static event manager instance to attach custom events
            $events = StaticEventManager::getInstance();
            
            // Lets make the service locator available globally
            $events->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array(
                $this,
                'addServiceLocatorGlobally'
            ), 111);
            
            // Activate the ACL as per the user's Choice
            $events->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array(
                $this,
                'authPreDispatch'
            ), 110);
            
            // Store the user identity whether the user is logged in or not.
            $events->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array(
                $this,
                'initializeUserIdentity'
            ), 109);
            
            // Store the user identity whether the user is logged in or not.
            $events->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array(
                $this,
                'initializeDbRowLevelAclOptions'
            ), 108);
        }
    }

    /**
     * MVC preDispatch Event
     * Adding service locator to the static options in Afc Module
     *
     * @param MvcEvent $event            
     * @return mixed
     */
    public function addServiceLocatorGlobally ($event)
    {
        $di = $event->getTarget()->getServiceLocator();
        StaticOptions::setServiceLocator($di);
    }

    /**
     * MVC preDispatch Event
     * Enable ACL features and authorizations
     *
     * @param MvcEvent $event            
     * @return mixed
     */
    public function authPreDispatch ($event)
    {
        try {
            $isAclEnabled = StaticOptions::getConfig("acl:enabled");
            if ($isAclEnabled) {
                $di = $event->getTarget()->getServiceLocator();
                $auth = $di->get('AfcCommons\Permissions\Event\Authentication');
                return $auth->preDispatch($event);
            }
        } catch (\Exception $ex) {
            throw $ex;
            // echo "<pre>";
            // var_dump($ex->getMessage());
            // var_dump($ex->getTraceAsString());
            // die();
        }
        return $event;
    }

    /**
     * MVC preDispatch Event
     * Save the user identity whether the user logged in or not.
     * If the user is not logged in then the user would be guest
     *
     * @param MvcEvent $event            
     * @return mixed
     */
    public function initializeUserIdentity ($event)
    {
        try {
            $isAclEnabled = StaticOptions::getConfig("acl:enabled");
            if ($isAclEnabled) {
                $di = $event->getTarget()->getServiceLocator();
                $userAuth = $di->get('AfcCommons\Permissions\Authentication\Authentication');
                if ($userAuth->hasIdentity()) {
                    $identity = $userAuth->getIdentity();
                } else {
                    $storage = new \Zend\Authentication\Storage\Session();
                    $identity = StaticOptions::getGuestUserObject();
                    $storage->write($identity);
                }
                StaticOptions::setCurrentUser($identity);
            }
        } catch (\Exception $ex) {}
        return $event;
    }

    /**
     * MVC preDispatch Event
     * Initialize options for DB row level ACL if enabled
     *
     * @param MvcEvent $event            
     * @return mixed
     */
    public function initializeDbRowLevelAclOptions ($event)
    {
        try {
            $isAclEnabled = StaticOptions::getConfig("acl:enabled");
            $isDbRowLevelAclEnabled = StaticOptions::getConfig("acl:options:db_row_level_acl_enabled");
            if ($isAclEnabled && $isDbRowLevelAclEnabled) {
                
                $defaultUserAccessMode = StaticOptions::getConfig("acl:options:db_row_default_permission:user_access_mode");
                StaticOptions::setDefaultUserAccessMode($defaultUserAccessMode);
                
                $defaultGroupAccessMode = StaticOptions::getConfig("acl:options:db_row_default_permission:group_access_mode");
                StaticOptions::setDefaultGroupAccessMode($defaultGroupAccessMode);
                
                $defaultOtherAccessMode = StaticOptions::getConfig("acl:options:db_row_default_permission:other_access_mode");
                StaticOptions::setDefaultOtherAccessMode($defaultOtherAccessMode);
                
                StaticOptions::setDbRowLevelAclEnabled(true);
            } else {
                StaticOptions::setDbRowLevelAclEnabled(false);
            }
        } catch (\Exception $ex) {
            StaticOptions::setDbRowLevelAclEnabled(false);
        }
        return $event;
    }

    /**
     * Autoloader configuration
     *
     * @return multitype:multitype:string
     */
    public function getAutoloaderConfig ()
    {
        // Set the Autoload Classmap file
        $fileAutoloadClassMap = $this->_dir . '/autoload_classmap.php';
        
        $autoloadClassMap = array();
        
        // Check if Autoload classmap for the module exists
        if (file_exists($fileAutoloadClassMap)) {
            
            // If Autoload classmap for the module exists
            $autoloadClassMap = require_once $fileAutoloadClassMap;
        }
        
        return array(
            'Zend\Loader\ClassMapAutoloader' => $autoloadClassMap,
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->_namespace => $this->_dir . '/src/' . $this->_namespace
                )
            )
        );
    }

    /**
     * Load service configuration and factory settings accordingly
     *
     * @return array
     */
    public function getServiceConfig ()
    {
        $serviceConfig = array();
        
        // Autoload General Function
        $standardFunctionPath = $this->_dir . '/src/' . $this->_namespace . '/Functions';
        
        if (is_dir($standardFunctionPath)) {
            $functions = scandir($standardFunctionPath);
            foreach ($functions as $function) {
                if (is_file($standardFunctionPath . DS . $function)) {
                    $key = $this->_namespace . "\\Functions\\" . str_replace(".php", "", $function);
                    $value = function  ($sm) use( $key)
                    {
                        $function = new $key();
                        return $function;
                    };
                    $serviceConfig[$key] = $value;
                }
            }
        }
        
        // Just add this once when this module is loaded
        if ($this->_namespace == __NAMESPACE__) {
            $serviceConfig['AfcCommons\Permissions\Event\Authentication'] = function  ($sm)
            {
                $function = new \AfcCommons\Permissions\Event\Authentication();
                return $function;
            };
        }
        return array(
            'factories' => $serviceConfig
        );
    }

    /**
     * Load automatic Configuration
     *
     * @return multitype:
     */
    public function getConfig ()
    {
        // Generate invokables array
        $invokables = array();
        
        // Initialize Route Array
        $routes = array();
        
        // Initialize Template Array
        $templatePathStack = array();
        
        $doctrineConfiguration = array(
            'driver' => array(
                $this->_namespace . '_driver' => array(
                    'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                    'cache' => 'array',
                    'paths' => array(
                        $this->_dir . '/src/' . $this->_namespace . '/Entity'
                    )
                ),
                'orm_default' => array(
                    'drivers' => array(
                        $this->_namespace . '\Entity' => $this->_namespace . '_driver'
                    )
                )
            )
        );
        
        // Get all controllers of the current module
        $controllerPath = $this->_dir . '/src/' . $this->_namespace . '/Controller/';
        $allController = array();
        if (is_dir($controllerPath)) {
            $allController = scandir($controllerPath);
        }
        
        // initialize translator for the module
        $translator = array();
        
        // Translator configurations
        $local_language_base_dir = $this->_dir . "/../language";
        
        // check if local language dir is available or not
        // If not then use the Application language dir
        if (is_dir($local_language_base_dir)) {
            $language_base_dir = $local_language_base_dir;
            $translator = array(
                'service_manager' => array(
                    'factories' => array(
                        'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory'
                    )
                ),
                'locale' => 'en_US',
                'translation_file_patterns' => array(
                    array(
                        'type' => 'gettext',
                        'base_dir' => $language_base_dir,
                        'pattern' => '%s.mo'
                    )
                )
            );
        }
        
        // Construct all necessary controller invokables list
        foreach ($allController as $controller) {
            if (is_file($controllerPath . DS . $controller)) {
                
                $controllerName = str_replace("Controller.php", "", $controller);
                $postfixControllerName = $controllerName . 'Controller';
                $key = $this->_namespace . "\\Controller\\" . $controllerName;
                $value = $this->_namespace . "\\Controller\\" . $postfixControllerName;
                $invokables[$key] = $value;
            }
        }
        
        // Construct Route
        $dashedNamespace = $this->convertToDash($this->_namespace);
        $routes[$dashedNamespace] = array(
            'type' => 'segment',
            'options' => array(
                'route' => "/" . $dashedNamespace . '[/:controller[/:action]]',
                'defaults' => array(
                    '__NAMESPACE__' => $this->_namespace . "\\Controller",
                    'controller' => $this->_namespace . "\\Controller\\Index",
                    'action' => 'index'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'wildcard' => array(
                    'type' => 'wildcard'
                )
            )
        );
        
        // Template Path Stack
        $templatePathStack[$dashedNamespace] = $this->_dir . '/view';
        
        // View manager configurations
        $viewManagerConfigurations = array(
            'display_not_found_reason' => true,
            'display_exceptions' => true,
            'doctype' => 'HTML5',
            'template_path_stack' => $templatePathStack
        );
        
        // Initialize Dependncy Injection as empty array
        $di = array();
        
        if ($this->_namespace == __NAMESPACE__) {
            
            $viewManagerConfigurations["strategies"] = array(
                'ViewJsonStrategy'
            );
            
            // declare di content only for once
            $di = array(
                'instance' => array(
                    // Acl Configuration with dependency injection
                    'AfcCommons\Permissions\Event\Authentication' => array(
                        'parameters' => array(
                            'userAuthenticationPlugin' => 'AfcCommons\Permissions\Authentication\Authentication'
                        )
                    )
                )
            );
        }
        
        $configArray = array(
            'di' => $di,
            // Doctrine Configurations
            'doctrine' => $doctrineConfiguration,
            
            // Invokable Controllers
            'controllers' => array(
                'invokables' => $invokables
            ),
            // Translator Options
            'translator' => $translator,
            
            // Route definition
            'router' => array(
                'routes' => $routes
            ),
            
            // View manager configurations
            'view_manager' => $viewManagerConfigurations
        );
        
        // Check for custom configurations
        $customConfigArray = array();
        $fileModuleConfig = $this->_dir . '/config/module.config.php';
        if (file_exists($fileModuleConfig)) {
            // If custom configurations exists then get the configurations
            $customConfigArray = require_once $fileModuleConfig;
        }
        
        $configArray = array_replace_recursive($configArray, (array) $customConfigArray);
        return $configArray;
    }

    /**
     * Convert ABC to a-b-c AbcDef to abc-def
     *
     * @param string $string            
     * @return string
     */
    private function convertToDash ($string)
    {
        $new_string = strtolower($string[0]);
        for ($i = 1; $i < strlen($string); $i ++) {
            if (preg_match('/^[A-Z]$/', $string[$i])) {
                $new_string = $new_string . "-" . strtolower($string[$i]);
            } else {
                $new_string = $new_string . $string[$i];
            }
        }
        return $new_string;
    }
}