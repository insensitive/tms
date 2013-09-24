<?php

namespace Trailer\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Trailer\Entity\Trailer;
use Trailer\Form\TrailerForm;
use Trailer\FormFilter\TrailerFormFilter;

class TrailerController extends AbstractController {
    
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
                        ) 
                ) 
        );
        $trailer = new Trailer ();
        $gridData = $trailer->getGridData ( $request, $options );
        $aaData = &$gridData ['aaData'];
        foreach ( $aaData as &$row ) {
            $allData = $row [5];
            
            // Setting Status
            $loweredStatus = strtolower ( $allData ['status'] );
            if ($loweredStatus == "active") {
                $status = '<span data-active="true" data-id="' . $allData ['trailer_id'] . '" class="toggleStatus btn btn-success">' . ucwords ( $loweredStatus ) . '</span>';
            } else {
                $status = '<span data-active="false" data-id="' . $allData ['trailer_id'] . '" class="toggleStatus btn btn-danger">' . ucwords ( $loweredStatus ) . '</span>';
            }
            $row [4] = $status;
            
            $bootstrapLinks = $this->BootstrapLinks ();
            $row [5] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'trailer/wildcard', array (
                    'controller' => 'trailer',
                    'action' => 'edit',
                    'id' => $allData ['trailer_id'] 
            ) ), false );
        }
        return new JsonModel ( $gridData );
    }
    public function addAction() {
        $form = new TrailerForm ();
        $request = $this->getRequest ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'trailer', array (
                'controller' => 'trailer',
                'action' => 'save' 
        ) ) );
        $view = new ViewModel ( array (
                'trailerForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Add Trailer' );
        $view->setTemplate ( 'trailer/trailer/add_edit' );
        return $view;
    }
    
    public function editAction() {
        $trailer_id = $this->params ()->fromRoute ( 'id' );
        $em = $this->getEntityManager ();
        $user = $em->getRepository ( 'Trailer\Entity\Trailer' )->findOneBy ( array (
                'trailer_id' => $trailer_id 
        ) );
        $form = new TrailerForm ();
        $form->setAttribute ( 'action', $this->url ()->fromRoute ( 'trailer', array (
                'controller' => 'trailer',
                'action' => 'save' 
        ) ) );
        
        $formValues = $user->getArrayCopy ();
        $form->populateValues ( $formValues );
        
        $view = new ViewModel ( array (
                'trailerForm' => $form 
        ) );
        $view->setVariable ( 'pageTitle', 'Edit Trailer' );
        $view->setTemplate ( 'trailer/trailer/add_edit' );
        return $view;
    }
    
    public function toggleStatusAction() {
        $request = $this->getRequest ();
        $response = array ();
        $response ["success"] = false;
        if ($request->isPost ()) {
            
            // Get the category id from post parameters
            $trailer_id = $request->getPost ( "trailer_id", false );
            
            if ($trailer_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
                
                try {
                    $trailer = $em->getRepository ( 'Trailer\Entity\Trailer' )->findOneBy ( array (
                            "trailer_id" => $trailer_id 
                    ) );
                    $oldStatus = strtolower ( $trailer->getStatus () );
                    $status = "ACTIVE";
                    if ($oldStatus == "active") {
                        $status = "INACTIVE";
                    }
                    $trailer->setStatus ( $status );
                    $em->persist ( $trailer );
                    $em->flush ();
                    // Commit the changes
                    $em->getConnection ()->commit ();
                    
                    $response ["success"] = true;
                    $response ["status"] = ucwords ( strtolower ( $status ) );
                    $response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
                    $response ["trailer_id"] = $trailer_id;
                
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
            $trailer_id = $request->getPost ( "trailer_id", false );
            
            if ($user_id) {
                // Get the entity manager
                $em = $this->getEntityManager ();
                // Start Transaction
                $em->getConnection ()->beginTransaction ();
                
                try {
                    $user = $em->getRepository ( 'Trailer\Entity\Trailer' )->findOneBy ( array (
                            "trailer_id" => $user_id 
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
        $formFilter = new TrailerFormFilter ();
        $form = new TrailerForm ();
        $form->setInputFilter ( $formFilter->getInputFilter () );
        $redirectUrl = false;
        if ($request->isPost ()) {
            $data = $request->getPost ();
            $form->setData ( $data );
            if ($form->isValid ()) {
                $data = $form->getData ();
                $em = $this->getEntityManager ();
                if ($data ['trailer_id'] != "") {
                    $trailer = $em->getRepository ( 'Trailer\Entity\Trailer' )->find ( $data ['trailer_id'] );
                } else {
                    $trailer = new Trailer ();
                }
                
                $trailer->exchangeArray ( $data );
                $em->persist ( $trailer );
                $em->flush ();
                $response ["success"] = true;
                $response ["message"] = "Trailer saved successfully.";
                $redirectUrl = $this->url ()->fromRoute ( 'trailer', array (
                        'controller' => 'trailer',
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
                'trailerForm' => $form,
                'response' => $response,
                'redirect_url' => $redirectUrl 
        ) );
        return $viewModel;
    }
}