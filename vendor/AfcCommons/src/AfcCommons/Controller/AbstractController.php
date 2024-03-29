<?php
namespace AfcCommons\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use AfcCommons\StaticOptions\StaticOptions;

abstract class AbstractController extends AbstractActionController
{

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $_user;

    protected $acceptCriteria = array(
        'Zend\View\Model\ViewModel' => array(
            'text/html'
        ),
        'Zend\View\Model\JsonModel' => array(
            'application/json'
        )
    );

    protected $_cache_controller = null;

    public function setEntityManager (EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager ()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    /**
     * Get the functions defined in "Functions folder"
     *
     * @param string $function_file_name            
     * @throws \Exception
     * @return Ambigous <object, multitype:>
     */
    public function getFunctions ($function_file_name = "")
    {
        if ((bool) $function_file_name) {
            return $this->getServiceLocator()->get($function_file_name);
        }
        throw new \Exception("File not found: '" . $function_file_name . "'");
    }

    public function getCurrentUser ()
    {
        if ($this->_user == null) {
            $this->_user = StaticOptions::getCurrentUser();
        }
        return $this->_user;
    }
}