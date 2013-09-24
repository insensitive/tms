<?php
namespace User\Controller;

use AfcCommons\Permissions\Acl\Acl;
use Zend\View\Model\JsonModel;
use User\Entity\User;
use AfcCommons\Controller\AbstractController;
use User\FormFilter\ManagerFormFilter;
use User\Form\ManagerForm;
use Zend\View\Model\ViewModel;

class ManagerController extends AbstractController
{
	public function dashboardAction(){
		
	}
    public function addAction ()
    {
        $form = new ManagerForm();
        $request = $this->getRequest();
        $form->setAttribute('action', $this->url()
            ->fromRoute('user', array(
            'controller' => 'manager',
            'action' => 'save'
        )));
        $view = new ViewModel(array(
            'addForm' => $form
        ));
        $view->setVariable('pageTitle', 'Add User');
        $view->setTemplate('user/manager/add_edit');
        return $view;
    }

    public function editAction ()
    {
        $user_id = $this->params()->fromRoute('id');
        $em = $this->getEntityManager();
        $user = $em->getRepository('User\Entity\User')->findOneBy(array(
            'user_id' => $user_id
        ));
        $form = new ManagerForm();
        $form->setAttribute('action', $this->url()
            ->fromRoute('user', array(
            'controller' => 'manager',
            'action' => 'save'
        )));
        $groupId = $user->getGroup()->getGroupId();
        $formValues = $user->getArrayCopy();
        $form->populateValues($formValues);
        $confirm = $form->get('confirm_password');
        $confirm->setValue($formValues['password']);
        $view = new ViewModel(array(
            'addForm' => $form
        ));
        $view->setVariable('pageTitle', 'Edit User');
        $view->setTemplate('user/manager/add_edit');
        return $view;
    }

    public function deleteAction ()
    {
        $request = $this->getRequest();
        $response = array();
        $response["success"] = false;
        if ($request->isPost()) {
            
            // Get the category id from post parameters
            $user_id = $request->getPost("user_id", false);
            
            if ($user_id) {
                // Get the entity manager
                $em = $this->getEntityManager();
                // Start Transaction
                $em->getConnection()->beginTransaction();
                
                try {
                    
                    $user = $em->getRepository('User\Entity\User')->findOneBy(array(
                        "user_id" => $user_id
                    ));
                    
                    $em->remove($user);
                    $em->flush();
                    
                    // Commit the changes
                    $em->getConnection()->commit();
                    
                    $response["success"] = true;
                } catch (\Exception $ex) {
                    $em->getConnection()->rollback();
                    $em->close();
                    $response['message'] = $ex->getTraceAsString();
                }
            }
        }
        return new JsonModel($response);
    }

    public function saveAction ()
    {
        $request = $this->getRequest();
        $formFilter = new ManagerFormFilter();
        $form = new ManagerForm();
        $form->setInputFilter($formFilter->getInputFilter());
        $redirectUrl = false;
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $em = $this->getEntityManager();
                if ($data['user_id'] != "") {
                    $user = $em->getRepository('User\Entity\User')->find($data['user_id']);
                } else {
                    $user = new User();                    
                }
                $group = $em->getRepository('AfcCommons\Entity\Group')->findOneBy(array(
                        'group_id' => 3
                ));
                $user->setGroup($group);
                $user->exchangeArray($data);
                $em->persist($user);
                $em->flush();
                $response["success"] = true;
                $response["message"] = "Client updated successfully.";
                $redirectUrl = $this->url()->fromRoute('user', array(
                    'controller' => 'manager',
                    'action' => 'index'
                ));
            } else {
                $errorMessages = $form->getMessages();
                $formattedErrorMessages = "";
                foreach ($errorMessages as $keyElement => $errorMessage) {
                    $errorText = array_pop($errorMessage);
                    switch ($keyElement) {
                        case 'username':
                            $formattedErrorMessages .= "Email : $errorText<br />";
                            break;
                        case 'confirm_password':
                            $formattedErrorMessages .= "Confirm Password : $errorText<br />";
                            break;
                        default:
                            $formattedErrorMessages .= "$keyElement : $errorText<br />";
                            break;
                    }
                }
                $response["message"] = $formattedErrorMessages;
            }
        }
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        $viewModel->setVariables(array(
            'registrationForm' => $form,
            'response' => $response,
            'redirect_url' => $redirectUrl
        ));
        return $viewModel;
    }

    public function gridAction ()
    {
        $request = $this->getRequest();
        $options = array(
            'column' => array(
                'query_result' => array(
                    'actions'
                )
            )
        );
        $groups = Acl::$groups;
        $user = new User();
        $response = $user->getGridData($request, $options);
        
        $aaData = &$response['aaData'];
        foreach ($aaData as $key => &$row) {
            $row[4] = ucwords(strtolower($groups[$row[4]]));
            $allData = $row[5];
            $bootstrapLinks = $this->BootstrapLinks();
            $row[5] = $bootstrapLinks->gridEditDelete($this->url()
                ->fromRoute('user/wildcard', array(
                'controller' => 'manager',
                'action' => 'edit',
                'id' => $allData['user_id']
            )), false);
        }
        $response['aaData'] = array_values($aaData);
        return new JsonModel($response);
    }
}