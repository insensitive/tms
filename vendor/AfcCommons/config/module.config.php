<?php
namespace AfcCommons;

return array(
    
    // Doctrine configuration for authentication
    'doctrine' => array(
        // authentication adapter parameters
        'authenticationadapter' => array(
            'odm_default' => array(
                'objectManager' => 'Doctrine\ORM\EntityManager',
                'identityClass' => 'User\Entity\User',
                'identityProperty' => 'email',
                'credentialProperty' => 'password'
                        )
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'error/403' => __DIR__ . '/../view/error/403.phtml'
        )
    ),
    'acl' => array(
        'guard' => array(
            'FileBank\Controller\File' => array(
                'download' => array(
                    'groups' => array(
                        'GUEST',
                        'MANAGER',
                        'ADMINISTRATOR'
                    )
                )
            ),
            'DoctrineORMModule\Yuml\YumlController' => array(
                'index' => array(
                    'groups' => array(
                        'GUEST',
                        'MANAGER',
                        'ADMINISTRATOR'
                    )
                )
            )
        )
    )
);