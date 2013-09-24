<?php
namespace User\Controller;

use AfcCommons\Controller\AbstractController;

class LogoutController extends AbstractController
{

    public function indexAction ()
    {
        $userAuth = $this->getServiceLocator ()->get ( 'AfcCommons\Permissions\Authentication\Authentication' );
		$userAuthService = $userAuth->getAuthService ();
		if ($userAuthService->hasIdentity ()) {
			$identity = $userAuthService->getIdentity ();
			if ($identity->getGroup()->getName() != \AfcCommons\Permissions\Acl\Acl::DEFAULT_ROLE) {
				$userAuthService->clearIdentity ();
			}
		}
		return $this->redirect ()->toRoute ( 'home' );
    }
}
