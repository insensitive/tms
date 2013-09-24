<?php

namespace Driver\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Driver\Entity\Driver;
use Driver\Form\DriverForm;
use Driver\FormFilter\DriverFormFilter;

class DriverController extends AbstractController {
    
    public function indexAction() {
    }
    
    public function gridAction() {
        $request = $this->getRequest ();
        $options = array (
                'column' => array (
                        'query_result' => array (
                                'actions' 
                        ),
                        'ignore' => array(
                                'address'
                        ) 
                ) 
        );
        $driver = new Driver ();
        $gridData = $driver->getGridData ( $request, $options );
        $aaData = &$gridData ['aaData'];
        foreach ( $aaData as &$row ) {
            $allData = $row [7];
            
            // Making Address
            $row [4] = $allData ['address1'] . ",<br />";            
            $row [4] .= ($allData ['address2'] != "" || $allData ['address2'] != null) ? $allData ['address2'] . ",<br />" : "";
            
            // Setting Status
            $loweredStatus = strtolower ( $allData ['status'] );
            if ($loweredStatus == "active") {
                $status = '<span data-active="true" data-id="' . $allData ['driver_id'] . '" class="toggleStatus btn btn-success">' . ucwords ( $loweredStatus ) . '</span>';
            } else {
                $status = '<span data-active="false" data-id="' . $allData ['driver_id'] . '" class="toggleStatus btn btn-danger">' . ucwords ( $loweredStatus ) . '</span>';
            }
            $row [6] = $status;
            
            $bootstrapLinks = $this->BootstrapLinks ();
            $row [7] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'driver/wildcard', array (
                    'controller' => 'driver',
                    'action' => 'edit',
                    'id' => $allData ['driver_id'] 
            ) ), false );
        }
        return new JsonModel ( $gridData );
    }
    public function addAction() {
        $form = new DriverForm ();
        $request = $this->getRequest ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'driver', array (
                'controller' => 'driver',
                'action' => 'save' 
        ) ) );
        $view = new ViewModel ( array (
                'driverForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Add Driver' );
        $view->setTemplate ( 'driver/driver/add_edit' );
        return $view;
    }
    
    public function editAction() {
        $driver_id = $this->params ()->fromRoute ( 'id' );
        $em = $this->getEntityManager ();
        $user = $em->getRepository ( 'Driver\Entity\Driver' )->findOneBy ( array (
                'driver_id' => $driver_id 
        ) );
        $form = new DriverForm ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'driver', array (
                'controller' => 'driver',
                'action' => 'save' 
        ) ) );
        
        $formValues = $user->getArrayCopy ();
        $form->populateValues ( $formValues );
        
        $view = new ViewModel ( array (
                'driverForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Edit Driver' );
        $view->setTemplate ( 'driver/driver/add_edit' );
        return $view;
    }
    
    public function toggleStatusAction() {
        $request = $this->getRequest ();
        $response = array ();
        $response ["success"] = false;
        if ($request->isPost ()) {
            
            // Get the category id from post parameters
            $driver_id = $request->getPost ( "driver_id", false );
            
            if ($driver_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
                
                try {
                    $driver = $em->getRepository ( 'Driver\Entity\Driver' )->findOneBy ( array (
                            "driver_id" => $driver_id 
                    ) );
                    $oldStatus = strtolower ( $driver->getStatus () );
                    $status = "ACTIVE";
                    if ($oldStatus == "active") {
                        $status = "INACTIVE";
                    }
                    $driver->setStatus ( $status );
                    $em->persist ( $driver );
                    $em->flush ();
                    // Commit the changes
                    $em->getConnection ()->commit ();
                    
                    $response ["success"] = true;
                    $response ["status"] = ucwords ( strtolower ( $status ) );
                    $response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
                    $response ["driver_id"] = $driver_id;
                
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
        $formFilter = new DriverFormFilter ();
        $form = new DriverForm ();
        $form->setInputFilter ( $formFilter->getInputFilter () );
        $redirectUrl = false;
        if ($request->isPost ()) {
            $data = $request->getPost ();
            $form->setData ( $data );
            if ($form->isValid ()) {
                $data = $form->getData ();
                $em = $this->getEntityManager ();
                if ($data ['driver_id'] != "") {
                    $driver = $em->getRepository ( 'Driver\Entity\Driver' )->find ( $data ['driver_id'] );
                } else {
                    $driver = new Driver ();
                }
                
                $driver->exchangeArray ( $data );
                $em->persist ( $driver );
                $em->flush ();
                $response ["success"] = true;
                $response ["message"] = "Driver saved successfully.";
                $redirectUrl = $this->url ()->fromRoute ( 'driver', array (
                        'controller' => 'driver',
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
                'driverForm' => $form,
                'response' => $response,
                'redirect_url' => $redirectUrl 
        ) );
        return $viewModel;
    }
}