<?php

namespace Truck\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Truck\Entity\Truck;
use Truck\Form\TruckForm;
use Truck\FormFilter\TruckFormFilter;

class TruckController extends AbstractController {
    
    public function indexAction() {
    }
    
    public function gridAction() {
        $request = $this->getRequest ();
        $options = array (
                'column' => array (
                        'query_result' => array (
                                'actions' 
                        ) 
                ) 
        );
        $truck = new Truck ();
        $gridData = $truck->getGridData ( $request, $options );
        $aaData = &$gridData ['aaData'];
        foreach ( $aaData as &$row ) {
            $allData = $row [5];
            // Making Address
            
            // Setting Status
            $loweredStatus = strtolower ( $allData ['status'] );
            if ($loweredStatus == "active") {
                $status = '<span data-active="true" data-id="' . $allData ['truck_id'] . '" class="toggleStatus btn btn-success">' . ucwords ( $loweredStatus ) . '</span>';
            } else {
                $status = '<span data-active="false" data-id="' . $allData ['truck_id'] . '" class="toggleStatus btn btn-danger">' . ucwords ( $loweredStatus ) . '</span>';
            }
            $row [4] = $status;
            
            $bootstrapLinks = $this->BootstrapLinks ();
            $row [5] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'truck/wildcard', array (
                    'controller' => 'truck',
                    'action' => 'edit',
                    'id' => $allData ['truck_id'] 
            ) ), false );
        }
        return new JsonModel ( $gridData );
    }
    public function addAction() {
        $form = new TruckForm ();
        $request = $this->getRequest ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'truck', array (
                'controller' => 'truck',
                'action' => 'save' 
        ) ) );
        $view = new ViewModel ( array (
                'truckForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Add Truck' );
        $view->setTemplate ( 'truck/truck/add_edit' );
        return $view;
    }
    
    public function editAction() {
        $truck_id = $this->params ()->fromRoute ( 'id' );
        $em = $this->getEntityManager ();
        $user = $em->getRepository ( 'Truck\Entity\Truck' )->findOneBy ( array (
                'truck_id' => $truck_id 
        ) );
        $form = new TruckForm ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'truck', array (
                'controller' => 'truck',
                'action' => 'save' 
        ) ) );
        
        $formValues = $user->getArrayCopy ();
        $form->populateValues ( $formValues );
        
        $view = new ViewModel ( array (
                'truckForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Edit Truck' );
        $view->setTemplate ( 'truck/truck/add_edit' );
        return $view;
    }
    
    public function toggleStatusAction() {
        $request = $this->getRequest ();
        $response = array ();
        $response ["success"] = false;
        if ($request->isPost ()) {
            
            // Get the category id from post parameters
            $truck_id = $request->getPost ( "truck_id", false );
            
            if ($truck_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
                
                try {
                    $truck = $em->getRepository ( 'Truck\Entity\Truck' )->findOneBy ( array (
                            "truck_id" => $truck_id 
                    ) );
                    $oldStatus = strtolower ( $truck->getStatus () );
                    $status = "ACTIVE";
                    if ($oldStatus == "active") {
                        $status = "INACTIVE";
                    }
                    $truck->setStatus ( $status );
                    $em->persist ( $truck );
                    $em->flush ();
                    // Commit the changes
                    $em->getConnection ()->commit ();
                    
                    $response ["success"] = true;
                    $response ["status"] = ucwords ( strtolower ( $status ) );
                    $response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
                    $response ["truck_id"] = $truck_id;
                
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
        $formFilter = new TruckFormFilter ();
        $form = new TruckForm ();
        $form->setInputFilter ( $formFilter->getInputFilter () );
        $redirectUrl = false;
        if ($request->isPost ()) {
            $data = $request->getPost ();
            $form->setData ( $data );
            if ($form->isValid ()) {
                $data = $form->getData ();
                $em = $this->getEntityManager ();
                if ($data ['truck_id'] != "") {
                    $truck = $em->getRepository ( 'Truck\Entity\Truck' )->find ( $data ['truck_id'] );
                } else {
                    $truck = new Truck ();
                }
                
                $truck->exchangeArray ( $data );
                $em->persist ( $truck );
                $em->flush ();
                $response ["success"] = true;
                $response ["message"] = "Truck saved successfully.";
                $redirectUrl = $this->url ()->fromRoute ( 'truck', array (
                        'controller' => 'truck',
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
                'truckForm' => $form,
                'response' => $response,
                'redirect_url' => $redirectUrl 
        ) );
        return $viewModel;
    }
}