<?php

namespace Transport\Controller;

use Zend\View\Model\JsonModel;
use AfcCommons\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Transport\Entity\Transport;
use Transport\Form\TransportForm;
use Transport\FormFilter\TransportFormFilter;
use AfcCommons\StaticOptions\StaticOptions;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class TransportController extends AbstractController {
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
								'way_bill',
								'invoice' 
						) 
				) 
		);
		$transport = new Transport ();
		$gridData = $transport->getGridData ( $request, $options );
		$aaData = &$gridData ['aaData'];
		foreach ( $aaData as &$row ) {
			$allData = $row [11];
			// Making Address
			
			// Setting Status
			$loweredStatus = strtolower ( $allData ['status'] );
			
			if ($loweredStatus == "completed") {
				$status = '<span data-active="true" data-id="' . $allData ['transport_id'] . '" class="toggleStatus btn btn-success">' . ucwords ( $loweredStatus ) . '</span>';
			} else {
				$status = '<span data-active="false" data-id="' . $allData ['transport_id'] . '" class="toggleStatus btn btn-warning">In Progress</span>';
			}
			$row [8] = $status;
			
			// Link to way bill
			$row [9] = '<a href="' . $this->url ()->fromRoute ( 'transport/wildcard', array (
					'controller' => 'transport',
					'action' => 'way-bill',
					'id' => $allData ['transport_id'] 
			) ) . '" class="btn btn-info" target="_blank">Way Bill</a>';
			
			$row [10] = "<div class='invoice-".$allData ['transport_id']."'>";
			if($loweredStatus == "completed") {
				$url = $this->url ()->fromRoute ( 'transport/wildcard', array (
						'controller' => 'transport',
						'action' => 'invoice',
						'id' 	=>  $allData ['transport_id']
				) );
				$row [10] .= '<a href="'.$url.'" class="btn btn-info" target="_blank">Invoice</a>';
			}
			$row [10] .= "</div>";
			
			$bootstrapLinks = $this->BootstrapLinks ();
			$row [11] = $bootstrapLinks->gridEditDelete ( $this->url ()->fromRoute ( 'transport/wildcard', array (
					'controller' => 'transport',
					'action' => 'edit',
					'id' => $allData ['transport_id'] 
			) ), false );
		}
		return new JsonModel ( $gridData );
	}
	public function addAction() {
		$form = new TransportForm ();
		$request = $this->getRequest ();
		$form->setAttribute ( 'action', $this->url ()->fromRoute ( 'transport', array (
				'controller' => 'transport',
				'action' => 'save' 
		) ) );
		$view = new ViewModel ( array (
				'transportForm' => $form 
		) );
		$view->setVariable ( 'pageTitle', 'Add Transport' );
		$view->setTemplate ( 'transport/transport/add_edit' );
		return $view;
	}
	public function editAction() {
		$transport_id = $this->params ()->fromRoute ( 'id' );
		$em = $this->getEntityManager ();
		$transport = $em->getRepository ( 'Transport\Entity\Transport' )->findOneBy ( array (
				'transport_id' => $transport_id 
		) );
		$form = new TransportForm ( null, true );
		$form->setAttribute ( 'action', $this->url ()->fromRoute ( 'transport', array (
				'controller' => 'transport',
				'action' => 'save' 
		) ) );
		
		$formValues = $transport->getArrayCopy ();
		$form->populateValues ( $formValues );
		
		$view = new ViewModel ( array (
				'transportForm' => $form 
		) );
		$view->setVariable ( 'pageTitle', 'Edit Transport' );
		$view->setTemplate ( 'transport/transport/add_edit' );
		return $view;
	}
	public function toggleStatusAction() {
		$request = $this->getRequest ();
		$response = array ();
		$response ["success"] = false;
		if ($request->isPost ()) {
			
			// Get the category id from post parameters
			$transport_id = $request->getPost ( "transport_id", false );
			
			if ($transport_id) {
				// Get the entity manager
				$em = $this->getEntityManager ();
				// Start Transaction
				$em->getConnection ()->beginTransaction ();
				
				try {
					$transport = $em->getRepository ( 'Transport\Entity\Transport' )->findOneBy ( array (
							"transport_id" => $transport_id 
					) );
					$oldStatus = strtolower ( $transport->getStatus () );
					$status = "IN_PROGRESS";
					if ($oldStatus == "in_progress") {
						$status = "COMPLETED";
					}
					$transport->setStatus ( $status );
					$em->persist ( $transport );
					$em->flush ();
					// Commit the changes
					$em->getConnection ()->commit ();
					
					$response ["success"] = true;
					$response ["status"] = ucwords ( strtolower ( $status ) );
					$response ["class"] = $status == "COMPLETED" ? "btn-success" : "btn-warning";
					$response ["transport_id"] = $transport_id;
					if($status == "COMPLETED") {
						$url = $this->url ()->fromRoute ( 'transport/wildcard', array (
								'controller' => 'transport',
								'action' => 'invoice',
								'id' 	=>  $transport_id
						) );
						$response ["invoice"] = '<a href="'.$url.'" class="btn btn-info" target="_blank">Invoice</a>';
					}
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
		$formFilter = new TransportFormFilter ();
		$form = new TransportForm ();
		$form->setInputFilter ( $formFilter->getInputFilter () );
		$redirectUrl = false;
		if ($request->isPost ()) {
			$data = $request->getPost ();
			$form->setData ( $data );
			if ($form->isValid ()) {
				$data = $form->getData ();
				$em = $this->getEntityManager ();
				if ($data ['transport_id'] != "") {
					$transport = $em->getRepository ( 'Transport\Entity\Transport' )->find ( $data ['transport_id'] );
				} else {
					$transport = new Transport ();
					$transport->setStatus ( 'IN_PROGRESS' );
					$transport->setStartTime ( new \DateTime () );
				}
				
				$transport->exchangeArray ( $data );
				$em->persist ( $transport );
				$em->flush ();
				$response ["success"] = true;
				$response ["message"] = "Transport saved successfully.";
				$redirectUrl = $this->url ()->fromRoute ( 'transport', array (
						'controller' => 'transport',
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
				'transportForm' => $form,
				'response' => $response,
				'redirect_url' => $redirectUrl 
		) );
		return $viewModel;
	}
	public function wayBillAction() {
		$this->layout ( 'print/layout' );
		$transport_id = $this->params ( 'id', false );
		if (! $transport_id) {
			return StaticOptions::get403Response ();
		}
		$em = $this->getEntityManager ();
		$transport = $em->getRepository ( 'Transport\Entity\Transport' )->findOneBy ( array (
				'transport_id' => $transport_id 
		) );
		return new ViewModel ( array (
				'transport' => $transport 
		) );
	}
	public function invoiceAction() {
		$this->layout ( 'print/layout' );
		$transport_id = $this->params ( 'id', false );
		if (! $transport_id) {
			return StaticOptions::get403Response ();
		}
		$em = $this->getEntityManager ();
		$transport = $em->getRepository ( 'Transport\Entity\Transport' )->findOneBy ( array (
				'transport_id' => $transport_id 
		) );
		return new ViewModel ( array (
				'transport' => $transport 
		) );
	}
	public function emailInvoiceAction() {
		$transport_id = $this->params ( 'id', false );
		if (! $transport_id) {
			return StaticOptions::get403Response ();
		}
		$em = $this->getEntityManager ();
		$transport = $em->getRepository ( 'Transport\Entity\Transport' )->findOneBy ( array (
				'transport_id' => $transport_id 
		) );
		
		$viewHelperManager = StaticOptions::getServiceLocator()->get('ViewHelperManager');
		$templateMap = StaticOptions::getConfig("view_manager:template_map");
		
		$resolver = new \Zend\View\Resolver\TemplateMapResolver($templateMap);
		
		$renderer = new PhpRenderer();
		$renderer->setHelperPluginManager($viewHelperManager);
        	$renderer->setResolver($resolver);
        	
		$view = new ViewModel ( array (
				'transport' => $transport 
		) );
		
		$view->setTemplate('email-invoice');
		$layout = new ViewModel();
		$layout->setTemplate('email/layout');
		$layout->setVariable('content',$renderer->render($view));
		$mailHtml = $renderer->render($layout);
		
		$smtpTransport = new SmtpTransport();
		$options   = new SmtpOptions(array(
		    'name'              => 'iwsenergygroup.com',
		    'host'              => 'mail.iwsenergygroup.com',
		    'connection_class'  => 'login',
		    'connection_config' => array(
		        'username' => 'billing@iwsenergygroup.com',
		        'password' => '1q2w3e4r',
		    ),
		));
		$smtpTransport->setOptions($options);
		$html = new MimePart($mailHtml);
		$html->type = "text/html";
		$body = new MimeMessage();
		$body->setParts(array($html));
		
		$message = new Message();
		$message->setBody($body);
		$message->setFrom('billing@iwsenergygroup.com', 'IWS Energy Group Customer Support');
		$message->addTo($transport->getCompany()->getEmail(), $transport->getCompany()->getName());
		$message->setSubject('Invoice');
		$smtpTransport->send($message);
		die;
	}
	public function destinationAddressesAction(){
		$options = array();
		$em = $this->getEntityManager();
		$company_id = $this->getRequest()->getPost()->get('company_id',false);
		if($company_id){
			$destinationAddresses = $em->getRepository('DestinationAddress\Entity\DestinationAddress')->findBy(array(
					'company_id' => $company_id
			));
			foreach($destinationAddresses as $destinationAddress){
				$options[$destinationAddress->getDestinationAddressId()] = $destinationAddress->getDescription();
			}
		}
		return new JsonModel ( $options );
	}
	public function fetchShipperInfoAction(){
		$options = array();
		$em = $this->getEntityManager();
		$shipper_id = $this->getRequest()->getPost()->get('shipper_id',false);
		if($shipper_id){
			$drivers = $em->getRepository('Driver\Entity\Driver')->findBy(array(
					'shipper_id' => $shipper_id
			));
			foreach($drivers as $driver){
				$options['driver'][$driver->getDriverId()] = $driver->getName();
			}
			
			$trucks = $em->getRepository('Truck\Entity\Truck')->findBy(array(
					'shipper_id' => $shipper_id
			));
			foreach($trucks as $truck){
				$options['truck'][$truck->getTruckId()] = $truck->getName();
			}
			
			$trailers = $em->getRepository('Trailer\Entity\Trailer')->findBy(array(
					'shipper_id' => $shipper_id
			));
			foreach($trailers as $trailer){
				$options['trailer'][$trailer->getTrailerId()] = $trailer->getPlateNumber();
			}
		}
		return new JsonModel ( $options );
	}
}