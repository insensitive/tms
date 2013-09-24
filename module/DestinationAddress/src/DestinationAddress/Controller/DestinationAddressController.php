<?php

namespace DestinationAddress\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use DestinationAddress\Entity\DestinationAddress;
use DestinationAddress\Form\DestinationAddressForm;
use DestinationAddress\FormFilter\DestinationAddressFormFilter;

class DestinationAddressController extends AbstractController {
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
		$destination_address = new DestinationAddress ();
		$gridData = $destination_address->getGridData ( $request, $options );
		$aaData = &$gridData ['aaData'];
		foreach ( $aaData as &$row ) {
			$allData = $row [6];
			$bootstrapLinks = $this->BootstrapLinks ();
			$row [6] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'destination-address/wildcard', array (
					'controller' => 'destination-address',
					'action' => 'edit',
					'id' => $allData ['destination_address_id'] 
			) ), false );
		}
		return new JsonModel ( $gridData );
	}
	public function addAction() {
		$form = new DestinationAddressForm ();
		$request = $this->getRequest ();
		$form->setAttribute ( 'action', $this->url ()->fromRoute ( 'destination-address', array (
				'controller' => 'destination-address',
				'action' => 'save' 
		) ) );
		$view = new ViewModel ( array (
				'destination_addressForm' => $form 
		) );
		$view->setVariable ( 'pageTitle', 'Add Destination Address' );
		$view->setTemplate ( 'destination-address/destination-address/add_edit' );
		return $view;
	}
	public function editAction() {
		$destination_address_id = $this->params ()->fromRoute ( 'id' );
		$em = $this->getEntityManager ();
		$truck = $em->getRepository ( 'DestinationAddress\Entity\DestinationAddress' )->findOneBy ( array (
				'destination_address_id' => $destination_address_id 
		) );
		$form = new DestinationAddressForm ();
		$form->setAttribute ( 'action', $this->url ()->fromRoute ( 'destination-address', array (
				'controller' => 'destination-address',
				'action' => 'save' 
		) ) );
		
		$formValues = $truck->getArrayCopy ();
		$form->populateValues ( $formValues );
		
		$view = new ViewModel ( array (
				'destination_addressForm' => $form 
		) );
		$view->setVariable ( 'pageTitle', 'Edit DestinationAddress' );
		$view->setTemplate ( 'destination-address/destination-address/add_edit' );
		return $view;
	}
	public function toggleStatusAction() {
		$request = $this->getRequest ();
		$response = array ();
		$response ["success"] = false;
		if ($request->isPost ()) {
			
			// Get the category id from post parameters
			$destination_address_id = $request->getPost ( "destination_address_id", false );
			
			if ($destination_address_id) {
				// Get the entity manager
				$em = $this->getEntityManager ();
				// Start Transaction
				$em->getConnection ()->beginTransaction ();
				
				try {
					$destination_address = $em->getRepository ( 'DestinationAddress\Entity\DestinationAddress' )->findOneBy ( array (
							"destination_address_id" => $destination_address_id 
					) );
					$oldStatus = strtolower ( $destination_address->getStatus () );
					$status = "ACTIVE";
					if ($oldStatus == "active") {
						$status = "INACTIVE";
					}
					$destination_address->setStatus ( $status );
					$em->persist ( $destination_address );
					$em->flush ();
					// Commit the changes
					$em->getConnection ()->commit ();
					
					$response ["success"] = true;
					$response ["status"] = ucwords ( strtolower ( $status ) );
					$response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
					$response ["destination_address_id"] = $destination_address_id;
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
		$formFilter = new DestinationAddressFormFilter ();
		$form = new DestinationAddressForm ();
		$form->setInputFilter ( $formFilter->getInputFilter () );
		$redirectUrl = false;
		if ($request->isPost ()) {
			$data = $request->getPost ();
			$form->setData ( $data );
			if ($form->isValid ()) {
				$data = $form->getData ();
				$em = $this->getEntityManager ();
				if ($data ['destination_address_id'] != "") {
					$destination_address = $em->getRepository ( 'DestinationAddress\Entity\DestinationAddress' )->find ( $data ['destination_address_id'] );
				} else {
					$destination_address = new DestinationAddress ();
				}
				
				$destination_address->exchangeArray ( $data );
				$em->persist ( $destination_address );
				$em->flush ();
				$response ["success"] = true;
				$response ["message"] = "Destination Address saved successfully.";
				$redirectUrl = $this->url ()->fromRoute ( 'destination-address', array (
						'controller' => 'destination-address',
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
				'destination_addressForm' => $form,
				'response' => $response,
				'redirect_url' => $redirectUrl 
		) );
		return $viewModel;
	}
}