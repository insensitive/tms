<?php

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class BootstrapLinks extends AbstractPlugin {
	/**
	 *
	 * @param string $editLink        	
	 * @param string $deleteLink        	
	 * @param array $options        	
	 * @return string
	 */
	public function gridEditDelete($editLink = "", $deleteLink = "", $options = array()) {
		
		// Setting Edit Options
		$editOptions = isset ( $options ['edit'] ) ? $options ['edit'] : array ();
		$editAttributes = isset ( $editOptions ['attributes'] ) ? $editOptions ['attributes'] : array ();
		$editClasses = isset ( $editOptions ['class'] ) ? $editOptions ['class'] : array ();
		$editAttributeString = '';
		$editClassString = '';
		foreach ( $editAttributes as $attribKey => $attribValue ) {
			$editAttributeString .= $attribKey . '="' . $attribValue . '" ';
		}
		foreach ( $editClasses as $editClass ) {
			$editClassString .= ' ' . $editClass;
		}
		
		// Setting delete options
		$deleteOptions = isset ( $options ['delete'] ) ? $options ['delete'] : array ();
		$deleteAttributes = isset ( $deleteOptions ['attributes'] ) ? $deleteOptions ['attributes'] : array ();
		$deleteClasses = isset ( $deleteOptions ['class'] ) ? $deleteOptions ['class'] : array ();
		$deleteAttributeString = '';
		$deleteClassString = '';
		foreach ( $deleteAttributes as $attribKey => $attribValue ) {
			$deleteAttributeString .= $attribKey . '="' . $attribValue . '" ';
		}
		foreach ( $deleteClasses as $deleteClass ) {
			$deleteClassString .= ' ' . $deleteClass;
		}
		
		if(!$deleteLink){
		    $span = "span5";
		} else {
		    $span = "span4";
		}
		$editLinkDiv = '' . //
  		'<div class="'.$span.'">
      		<a href="' . $editLink . '" ' . $editAttributeString . ' class="btn' . $editClassString . '">
	      		<i class="icon-edit"></i>
	      		<span class="visible-desktop hidden-tablet hidden-mobile"> Edit</span>
      		</a>
  		</div>';
		
		if(!$editLink){
		    $span = "span5";
		} else {
		    $span = "span4";
		}
		$deleteLinkDiv = ''. //
  		'<div class="'.$span.'">
			<a href="' . $deleteLink . '" ' . $deleteAttributeString . ' class="btn btn-primary' . $deleteClassString . '">
				<i class="white-icon icon-trash"></i>
				<span class="visible-desktop hidden-tablet hidden-mobile"> Delete</span>
			</a>
		</div>';
		
		      		
		$bootstrapLinks = '<div class="row-fluid">'. ($editLink?$editLinkDiv:"") . ($deleteLink?$deleteLinkDiv:"") . "</div>"; 
		return $bootstrapLinks;
	}
}