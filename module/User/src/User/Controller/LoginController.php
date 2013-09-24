<?php
namespace User\Controller;

use AfcCommons\Controller\AbstractController;
use AfcCommons\StaticOptions\StaticOptions;
use DoctrineModule\Authentication\Adapter\ObjectRepository;
use AfcCommons\Permissions\Acl\Acl;

class LoginController extends AbstractController
{

    public function indexAction ()
    {
        $this->layout('login/layout');
        $redirectUrl = "/";
        $loginFormFilter = new \User\FormFilter\LoginFormFilter();
        
        $loginForm = new \User\Form\LoginForm();
        $loginForm->setInputFilter($loginFormFilter->getInputFilter());
        $request = $this->getRequest();
        $response = array();
        $response["success"] = false;
        $response["message"] = "Invalid Credentials";
        if ($request->isPost()) {
            $loginForm->setData($request->getPost());
            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                
                // Configure Doctrine Adapter for authentication
                $doctrineAdapter = new ObjectRepository();
                $config = $this->getServiceLocator()->get('Config');
                $config = $config['doctrine']['authenticationadapter']['odm_default'];
                
                if (is_string($config['objectManager'])) {
                    $config['objectManager'] = $this->getServiceLocator()->get($config['objectManager']);
                }
                // Initialize the Doctrine Adapter with options
                $doctrineAdapter->setOptions($config);
                
                // Set the received credentials
                $doctrineAdapter->setIdentityValue((string) $data['email']);
                $doctrineAdapter->setCredentialValue((string) $data['password']);
                
                
                // Get the user auth mechanism
                $userAuth = $this->getServiceLocator()->get('AfcCommons\Permissions\Authentication\Authentication');
                
                // Tell the user auth about the doctrine adapter
                $userAuth->setAuthAdapter($doctrineAdapter);
                $authService = $userAuth->getAuthService();
                $authenticationResult = $authService->authenticate($userAuth->getAuthAdapter());
                if ($authenticationResult->isValid()) {
                    
                    $user = $authenticationResult->getIdentity();
                    
                    StaticOptions::setCurrentUser($user);
                    if(Acl::$groups[$user->getGroup()->getGroupId()] == "ADMINISTRATOR"){
                        $redirectUrl = $this->url()->fromRoute('user', array(
							'controller' => 'admin',
							'action' => 'dashboard'
						));
                    } else {
                    	$redirectUrl = $this->url()->fromRoute('user', array(
	                        'controller' => 'manager',
	                        'action' => 'dashboard'
	                    ));
                    }
                    $response["success"] = true;
                    $response["message"] = "Login successfull. Redirecting to dashboard";
                    
                    if(!$request->isXmlHttpRequest()){
                    	return $this->redirect()->toUrl($redirectUrl);
                    }
                }
            } else {
                $response["message"] = $loginForm->getMessages();
            }
        }
        
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        
        $viewModel->setVariables(array(
            'loginForm' => $loginForm,
            'response' => $response,
            'redirect_url' => $redirectUrl 
        ));
        
        return $viewModel;
    }
}
