<?php

namespace SourceAddress\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use SourceAddress\Entity\SourceAddress;
use SourceAddress\Form\SourceAddressForm;
use SourceAddress\FormFilter\SourceAddressFormFilter;

class SourceAddressController extends AbstractController {
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
		$source_address = new SourceAddress ();
		$gridData = $source_address->getGridData ( $request, $options );
		$aaData = &$gridData ['aaData'];
		foreach ( $aaData as &$row ) {
			$allData = $row [5];
			
			$bootstrapLinks = $this->BootstrapLinks ();
			$row [5] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'source-address/wildcard', array (
					'controller' => 'source-address',
					'action' => 'edit',
					'id' => $allData ['source_address_id'] 
			) ), false );
		}
		return new JsonModel ( $gridData );
	}
	public function addAction() {
		$form = new SourceAddressForm ();
		$request = $this->getRequest ();
		$form->setAttribute ( 'action', $this->url ()->fromRoute ( 'source-address', array (
				'controller' => 'source-address',
				'action' => 'save' 
		) ) );
		$view = new ViewModel ( array (
				'source_addressForm' => $form 
		) );
		$view->setVariable ( 'pageTitle', 'Add SourceAddress' );
		$view->setTemplate ( 'source-address/source-address/add_edit' );
		return $view;
	}
	public function editAction() {
		$source_address_id = $this->params ()->fromRoute ( 'id' );
		$em = $this->getEntityManager ();
		$truck = $em->getRepository ( 'SourceAddress\Entity\SourceAddress' )->findOneBy ( array (
				'source_address_id' => $source_address_id 
		) );
		$form = new SourceAddressForm ();
		$form->setAttribute ( 'action', $this->url ()->fromRoute ( 'source-address', array (
				'controller' => 'source-address',
				'action' => 'save' 
		) ) );
		
		$formValues = $truck->getArrayCopy ();
		$form->populateValues ( $formValues );
		
		$view = new ViewModel ( array (
				'source_addressForm' => $form 
		) );
		$view->setVariable ( 'pageTitle', 'Edit SourceAddress' );
		$view->setTemplate ( 'source-address/source-address/add_edit' );
		return $view;
	}
	public function toggleStatusAction() {
		$request = $this->getRequest ();
		$response = array ();
		$response ["success"] = false;
		if ($request->isPost ()) {
			
			// Get the category id from post parameters
			$source_address_id = $request->getPost ( "source_address_id", false );
			
			if ($source_address_id) {
				// Get the entity manager
				$em = $this->getEntityManager ();
				// Start Transaction
				$em->getConnection ()->beginTransaction ();
				
				try {
					$source_address = $em->getRepository ( 'SourceAddress\Entity\SourceAddress' )->findOneBy ( array (
							"source_address_id" => $source_address_id 
					) );
					$oldStatus = strtolower ( $source_address->getStatus () );
					$status = "ACTIVE";
					if ($oldStatus == "active") {
						$status = "INACTIVE";
					}
					$source_address->setStatus ( $status );
					$em->persist ( $source_address );
					$em->flush ();
					// Commit the changes
					$em->getConnection ()->commit ();
					
					$response ["success"] = true;
					$response ["status"] = ucwords ( strtolower ( $status ) );
					$response ["class"] = $status == "ACTIVE" ? "btn-success" : "btn-danger";
					$response ["source_address_id"] = $source_address_id;
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
		$formFilter = new SourceAddressFormFilter ();
		$form = new SourceAddressForm ();
		$form->setInputFilter ( $formFilter->getInputFilter () );
		$redirectUrl = false;
		if ($request->isPost ()) {
			$data = $request->getPost ();
			$form->setData ( $data );
			if ($form->isValid ()) {
				$data = $form->getData ();
				$em = $this->getEntityManager ();
				if ($data ['source_address_id'] != "") {
					$source_address = $em->getRepository ( 'SourceAddress\Entity\SourceAddress' )->find ( $data ['source_address_id'] );
				} else {
					$source_address = new SourceAddress ();
				}
				
				$source_address->exchangeArray ( $data );
				$em->persist ( $source_address );
				$em->flush ();
				$response ["success"] = true;
				$response ["message"] = "Source Address saved successfully.";
				$redirectUrl = $this->url ()->fromRoute ( 'source-address', array (
						'controller' => 'source-address',
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
				'source_addressForm' => $form,
				'response' => $response,
				'redirect_url' => $redirectUrl 
		) );
		return $viewModel;
	}
}