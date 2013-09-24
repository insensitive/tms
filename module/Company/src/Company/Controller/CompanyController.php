<?php

namespace Company\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Company\Entity\Company;
use Company\Form\CompanyForm;
use Company\FormFilter\CompanyFormFilter;

class CompanyController extends AbstractController {
    
    public function indexAction() {
    }
    
    public function gridAction() {
        $request = $this->getRequest ();
        $options = array (
                'column' => array (
                        'query_result' => array (
                                'actions' 
                        ),
                        'ignore' => array (
                                'address',
                                'office',
                                'cell' 
                        ) 
                ) 
        );
        $company = new Company ();
        $gridData = $company->getGridData ( $request, $options );
        $aaData = &$gridData ['aaData'];
        foreach ( $aaData as &$row ) {
            $allData = $row [8];
            // Making Address
            $row [2] = $allData ['address1'] . ",<br />";
            
            $row [2] .= ($allData ['address2'] != "" || $allData ['address2'] != null) ? $allData ['address2'] . ",<br />" : "";
            $row [2] .= $allData ['city'] . ",<br />";
            $row [2] .= $allData ['provision'];
            
            // Getting Office With Extension
            $phoneOffice = $allData ['phone_office'] . " ";
            $phoneOffice .= ($allData ['phone_office_ext'] == null || $allData ['phone_office_ext'] == "") ? "" : "<br /><span class='small muted'>(Ext: " . $allData ['phone_office_ext'] . ")</span>";
            $row [3] = $phoneOffice;
            
            // Getting Cell Phone
            $row [4] = $allData ['phone_cell'];
            
            // Setting Status
            $loweredStatus = strtolower ( $allData ['status'] );
            if($loweredStatus == "active"){
                $status = '<span data-active="true" data-id="'.$allData['company_id'].'" class="toggleStatus btn btn-success">'.ucwords($loweredStatus).'</span>';
            } else {
                $status = '<span data-active="false" data-id="'.$allData['company_id'].'" class="toggleStatus btn btn-danger">'.ucwords($loweredStatus).'</span>';
            }
            $row [7] = $status;
            
            $bootstrapLinks = $this->BootstrapLinks ();
            $row [8] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'company/wildcard', array (
                    'controller' => 'company',
                    'action' => 'edit',
                    'id' => $allData ['company_id'] 
            ) ), false );
        }
        return new JsonModel ( $gridData );
    }
    public function addAction() {
        $form = new CompanyForm ();
        $request = $this->getRequest ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'company', array (
                'controller' => 'company',
                'action' => 'save' 
        ) ) );
        $view = new ViewModel ( array (
                'companyForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Add Company' );
        $view->setTemplate ( 'company/company/add_edit' );
        return $view;
    }
    
    public function editAction() {
        $company_id = $this->params ()->fromRoute ( 'id' );
        $em = $this->getEntityManager ();
        $user = $em->getRepository ( 'Company\Entity\Company' )->findOneBy ( array (
                'company_id' => $company_id 
        ) );
        $form = new CompanyForm ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'company', array (
                'controller' => 'company',
                'action' => 'save' 
        ) ) );
        
        $formValues = $user->getArrayCopy ();
        $form->populateValues ( $formValues );
        
        $view = new ViewModel ( array (
                'companyForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Edit Company' );
        $view->setTemplate ( 'company/company/add_edit' );
        return $view;
    }
    
    public function toggleStatusAction(){
        $request = $this->getRequest ();
        $response = array ();
        $response ["success"] = false;
        if ($request->isPost ()) {
        
            // Get the category id from post parameters
            $company_id = $request->getPost ( "company_id", false );
        
            if ($company_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
        
                try {
                    $company = $em->getRepository ( 'Company\Entity\Company' )->findOneBy ( array (
                            "company_id" => $company_id
                    ) );
                    $oldStatus = strtolower($company->getStatus());
                    $status = "ACTIVE";
                    if($oldStatus == "active"){
                        $status = "INACTIVE";
                    }
                    $company->setStatus($status);
                    $em->persist($company);
                    $em->flush ();
                    // Commit the changes
                    $em->getConnection ()->commit ();
        
                    $response ["success"] = true;
                    $response ["status"] = ucwords(strtolower($status));
                    $response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
                    $response ["company_id"] = $company_id;
                    
                } catch ( \Exception $ex ) {
                    $em->getConnection ()->rollback ();
                    $em->close ();
                    $response ['message'] = $ex->getTraceAsString ();
                }
            }
        }
        return new JsonModel ( $response );
    }
    
    public function deleteAction() {
        $request = $this->getRequest ();
        $response = array ();
        $response ["success"] = false;
        if ($request->isPost ()) {
            
            // Get the category id from post parameters
            $user_id = $request->getPost ( "user_id", false );
            
            if ($user_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
                
                try {
                    $user = $em->getRepository ( 'User\Entity\User' )->findOneBy ( array (
                            "user_id" => $user_id 
                    ) );
                    
                    $em->remove ( $user );
                    $em->flush ();
                    
                    // Commit the changes
                    $em->getConnection ()->commit ();
                    
                    $response ["success"] = true;
                } catch ( \Exception $ex ) {
                    $em->getConnection ()->rollback ();
                    $em->close ();
                    $response ['message'] = $ex->getTraceAsString ();
                }
            }
        }
        return new JsonModel ( $response );
    }
    
    public function saveAction() {
        $request = $this->getRequest ();
        $formFilter = new CompanyFormFilter ();
        $form = new CompanyForm ();
        $form->setInputFilter ( $formFilter->getInputFilter () );
        $redirectUrl = false;
        if ($request->isPost ()) {
            $data = $request->getPost ();
            $form->setData ( $data );
            if ($form->isValid ()) {
                $data = $form->getData ();
                $em = $this->getEntityManager ();
                if ($data ['company_id'] != "") {
                    $company = $em->getRepository ( 'Company\Entity\Company' )->find ( $data ['company_id'] );
                } else {
                    $company = new Company ();
                }
                
                $company->exchangeArray ( $data );
                $em->persist ( $company );
                $em->flush ();
                $response ["success"] = true;
                $response ["message"] = "Company saved successfully.";
                $redirectUrl = $this->url ()->fromRoute ( 'company', array (
                        'controller' => 'company',
                        'action' => 'index' 
                ) );
            } else {
                $errorMessages = $form->getMessages ();
                $formattedErrorMessages = "";
                foreach ( $errorMessages as $keyElement => $errorMessage ) {
                    $errorText = array_pop ( $errorMessage );
                    $formattedErrorMessages .= "$keyElement : $errorText<br />";
                
                }
                $response ["message"] = $formattedErrorMessages;
            }
        }
        $viewModel = $this->acceptableViewModelSelector ( $this->acceptCriteria );
        $viewModel->setVariables ( array (
                'companyForm' => $form,
                'response' => $response,
                'redirect_url' => $redirectUrl 
        ) );
        return $viewModel;
    }
}