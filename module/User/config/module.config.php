<?php
return array(
    'acl' => array(
        'guard' => array(
                'User\Controller\Logout' => array(
                        'index' => array(
                                'groups' => array(
                                        'ADMINISTRATOR',
                                        'MANAGER'
                                )
                        )
                )
        ),
        '403-Redirect' => array(
            array(
                "resource" => 'User\Controller\Login',
                "action" => "index",
                "redirect-group" => array(
                    "ADMINISTRATOR" => "/user/admin/dashboard"
                )
            )
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'user/login'           => __DIR__ . '/../view/layout/layout.phtml',
        )
    ),
);