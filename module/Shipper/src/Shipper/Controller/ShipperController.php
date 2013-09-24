<?php

namespace Shipper\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Shipper\Entity\Shipper;
use Shipper\Form\ShipperForm;
use Shipper\FormFilter\ShipperFormFilter;

class ShipperController extends AbstractController {
    
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
        $shipper = new Shipper();
        $gridData = $shipper->getGridData ( $request, $options );
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
                $status = '<span data-active="true" data-id="'.$allData['shipper_id'].'" class="toggleStatus btn btn-success">'.ucwords($loweredStatus).'</span>';
            } else {
                $status = '<span data-active="false" data-id="'.$allData['shipper_id'].'" class="toggleStatus btn btn-danger">'.ucwords($loweredStatus).'</span>';
            }
            $row [7] = $status;
            
            $bootstrapLinks = $this->BootstrapLinks ();
            $row [8] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'shipper/wildcard', array (
                    'controller' => 'shipper',
                    'action' => 'edit',
                    'id' => $allData ['shipper_id'] 
            ) ), false );
        }
        return new JsonModel ( $gridData );
    }
    public function addAction() {
        $form = new ShipperForm();
        $request = $this->getRequest ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'shipper', array (
                'controller' => 'shipper',
                'action' => 'save' 
        ) ) );
        $view = new ViewModel ( array (
                'shipperForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Add Shipper' );
        $view->setTemplate ( 'shipper/shipper/add_edit' );
        return $view;
    }
    
    public function editAction() {
        $shipper_id = $this->params ()->fromRoute ( 'id' );
        $em = $this->getEntityManager ();
        $shipper = $em->getRepository ( 'Shipper\Entity\Shipper' )->findOneBy ( array (
                'shipper_id' => $shipper_id 
        ) );
        $form = new ShipperForm ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'shipper', array (
                'controller' => 'shipper',
                'action' => 'save' 
        ) ) );
        
        $formValues = $shipper->getArrayCopy ();
        $form->populateValues ( $formValues );
        
        $view = new ViewModel ( array (
                'shipperForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Edit Shipper' );
        $view->setTemplate ( 'shipper/shipper/add_edit' );
        return $view;
    }
    
    public function toggleStatusAction(){
        $request = $this->getRequest ();
        $response = array ();
        $response ["success"] = false;
        if ($request->isPost ()) {
        
            // Get the category id from post parameters
            $shipper_id = $request->getPost ( "shipper_id", false );
        
            if ($shipper_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
        
                try {
                    $shipper = $em->getRepository ( 'Shipper\Entity\Shipper' )->findOneBy ( array (
                            "shipper_id" => $shipper_id
                    ) );
                    $oldStatus = strtolower($shipper->getStatus());
                    $status = "ACTIVE";
                    if($oldStatus == "active"){
                        $status = "INACTIVE";
                    }
                    $shipper->setStatus($status);
                    $em->persist($shipper);
                    $em->flush ();
                    // Commit the changes
                    $em->getConnection ()->commit ();
        
                    $response ["success"] = true;
                    $response ["status"] = ucwords(strtolower($status));
                    $response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
                    $response ["shipper_id"] = $shipper_id;
                    
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
        $formFilter = new ShipperFormFilter();
        $form = new ShipperForm ();
        $form->setInputFilter ( $formFilter->getInputFilter () );
        $redirectUrl = false;
        if ($request->isPost ()) {
            $data = $request->getPost ();
            $form->setData ( $data );
            if ($form->isValid ()) {
                $data = $form->getData ();
                $em = $this->getEntityManager ();
                if ($data ['shipper_id'] != "") {
                    $shipper = $em->getRepository ( 'Shipper\Entity\Shipper' )->find ( $data ['shipper_id'] );
                } else {
                    $shipper = new Shipper();
                }
                
                $shipper->exchangeArray ( $data );
                $em->persist ( $shipper );
                $em->flush ();
                $response ["success"] = true;
                $response ["message"] = "Shipper saved successfully.";
                $redirectUrl = $this->url ()->fromRoute ( 'shipper', array (
                        'controller' => 'shipper',
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
                'shipperForm' => $form,
                'response' => $response,
                'redirect_url' => $redirectUrl 
        ) );
        return $viewModel;
    }
}