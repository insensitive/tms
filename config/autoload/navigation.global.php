<?php
return array (
        'navigation' => array (
                'default' => array (
                        array (
                                'label' => 'Login',
                                'route' => 'user',
                                'controller' => 'login',
                                'action' => 'index',
                                'resource' => 'User\Controller\Login',
                                'privilege' => 'index',
                                'order' => 1 
                        ),
                        array (
                                'label' => 'Admin Dashboard',
                                'route' => 'user',
                                'controller' => 'admin',
                                'action' => 'dashboard',
                                'resource' => 'User\Controller\Admin',
                                'privilege' => 'dashboard',
                                'visible' => false 
                        ),
                        array (
                                'label' => 'Customers',
                                'route' => 'company',
                                'controller' => 'company',
                                'action' => 'index',
                                'resource' => 'Company\Controller\Company',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Add',
                                                'route' => 'company',
                                                'controller' => 'company',
                                                'action' => 'add',
                                                'resource' => 'Company\Controller\Company',
                                                'privilege' => 'add',
                                                'visible' => false 
                                        ),
                                        array (
                                                'label' => 'Edit',
                                                'route' => 'company/wildcard',
                                                'controller' => 'company',
                                                'action' => 'edit',
                                                'resource' => 'Company\Controller\Company',
                                                'privilege' => 'edit',
                                                'visible' => false
                                        ),
                                )
                        ),
                        array (
                                'label' => 'Shippers',
                                'route' => 'shipper',
                                'controller' => 'shipper',
                                'action' => 'index',
                                'resource' => 'Shipper\Controller\Shipper',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Add',
                                                'route' => 'shipper',
                                                'controller' => 'shipper',
                                                'action' => 'add',
                                                'resource' => 'Shipper\Controller\Shipper',
                                                'privilege' => 'add',
                                                'visible' => false
                                        ),
                                        array (
                                                'label' => 'Edit',
                                                'route' => 'shipper/wildcard',
                                                'controller' => 'shipper',
                                                'action' => 'edit',
                                                'resource' => 'Shipper\Controller\Shipper',
                                                'privilege' => 'edit',
                                                'visible' => false
                                        ),
                                )
                        ),
                		array (
                				'label' => 'Source',
                				'route' => 'source-address',
                				'controller' => 'source-address',
                				'action' => 'index',
                				'resource' => 'SourceAddress\Controller\SourceAddress',
                				'privilege' => 'index',
                				'pages' => array (
                						array (
                								'label' => 'Add',
                								'route' => 'source-address',
                								'controller' => 'source-address',
                								'action' => 'add',
                								'resource' => 'SourceAddress\Controller\SourceAddress',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit',
                								'route' => 'source-address',
                								'controller' => 'source-address',
                								'action' => 'edit',
                								'resource' => 'SourceAddress\Controller\SourceAddress',
                								'privilege' => 'edit',
                								'visible' => false
                						),
                				)
                				 
                		),
                		array (
                				'label' => 'Destination',
                				'route' => 'destination-address',
                				'controller' => 'destination-address',
                				'action' => 'index',
                				'resource' => 'DestinationAddress\Controller\DestinationAddress',
                				'privilege' => 'index',
                				'pages' => array (
                						array (
                								'label' => 'Add',
                								'route' => 'destination-address',
                								'controller' => 'destination-address',
                								'action' => 'add',
                								'resource' => 'DestinationAddress\Controller\DestinationAddress',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit',
                								'route' => 'destination-address',
                								'controller' => 'destination-address',
                								'action' => 'edit',
                								'resource' => 'DestinationAddress\Controller\DestinationAddress',
                								'privilege' => 'edit',
                								'visible' => false
                						),
                				)
                				 
                		),
                        array (
                                'label' => 'Trucks',
                                'route' => 'truck',
                                'controller' => 'truck',
                                'action' => 'index',
                                'resource' => 'Truck\Controller\Truck',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Add',
                                                'route' => 'truck',
                                                'controller' => 'truck',
                                                'action' => 'add',
                                                'resource' => 'Truck\Controller\Truck',
                                                'privilege' => 'add',
                                                'visible' => false
                                        ),
                                        array (
                                                'label' => 'Edit',
                                                'route' => 'truck/wildcard',
                                                'controller' => 'truck',
                                                'action' => 'edit',
                                                'resource' => 'Truck\Controller\Truck',
                                                'privilege' => 'edit',
                                                'visible' => false
                                        ),
                                )
                        ),
                        array (
                                'label' => 'Trailer',
                                'route' => 'trailer',
                                'controller' => 'trailer',
                                'action' => 'index',
                                'resource' => 'Trailer\Controller\Trailer',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Add',
                                                'route' => 'trailer',
                                                'controller' => 'trailer',
                                                'action' => 'add',
                                                'resource' => 'Trailer\Controller\Trailer',
                                                'privilege' => 'add',
                                                'visible' => false
                                        ),
                                        array (
                                                'label' => 'Edit',
                                                'route' => 'trailer/wildcard',
                                                'controller' => 'trailer',
                                                'action' => 'edit',
                                                'resource' => 'Trailer\Controller\Trailer',
                                                'privilege' => 'edit',
                                                'visible' => false
                                        ),
                                )
                        ),
                        array (
                                'label' => 'Drivers',
                                'route' => 'driver',
                                'controller' => 'driver',
                                'action' => 'index',
                                'resource' => 'Driver\Controller\Driver',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Add',
                                                'route' => 'driver',
                                                'controller' => 'driver',
                                                'action' => 'add',
                                                'resource' => 'Driver\Controller\Driver',
                                                'privilege' => 'add',
                                                'visible' => false
                                        ),
                                        array (
                                                'label' => 'Edit',
                                                'route' => 'driver/wildcard',
                                                'controller' => 'driver',
                                                'action' => 'edit',
                                                'resource' => 'Driver\Controller\Driver',
                                                'privilege' => 'edit',
                                                'visible' => false
                                        ),
                                )
                        ),
                		array (
                				'label' => 'Transports',
                				'route' => 'transport',
                				'controller' => 'transport',
                				'action' => 'index',
                				'resource' => 'Transport\Controller\Transport',
                				'privilege' => 'index',
                				'pages' => array (
                						array (
                								'label' => 'Add',
                								'route' => 'transport',
                								'controller' => 'transport',
                								'action' => 'add',
                								'resource' => 'Transport\Controller\Transport',
                								'privilege' => 'add',
                								'visible' => false
                						),
                						array (
                								'label' => 'Edit',
                								'route' => 'transport/wildcard',
                								'controller' => 'transport',
                								'action' => 'edit',
                								'resource' => 'Transport\Controller\Transport',
                								'privilege' => 'edit',
                								'visible' => false
                						),
                				)
                		),
                        array (
                                'label' => 'Managers',
                                'route' => 'user',
                                'controller' => 'manager',
                                'action' => 'index',
                                'resource' => 'User\Controller\Manager',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Add',
                                                'route' => 'user',
                                                'controller' => 'manager',
                                                'action' => 'add',
                                                'resource' => 'User\Controller\Manager',
                                                'privilege' => 'add',
                                                'visible' => false 
                                        ),
                                        array (
                                                'label' => 'Edit',
                                                'route' => 'user',
                                                'controller' => 'manager',
                                                'action' => 'edit',
                                                'resource' => 'User\Controller\Manager',
                                                'privilege' => 'edit',
                                                'visible' => false
                                        ),
                                )
                                 
                        ),
                        array (
                                'label' => 'Product Management',
                                'route' => 'product',
                                'controller' => 'product',
                                'action' => 'index',
                                'resource' => 'Product\Controller\Product',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Products',
                                                'route' => 'product',
                                                'controller' => 'product',
                                                'action' => 'index',
                                                'resource' => 'Product\Controller\Product',
                                                'privilege' => 'index',
                                                'pages' => array (
                                                        array (
                                                                'label' => 'Add Product',
                                                                'route' => 'product',
                                                                'controller' => 'product',
                                                                'action' => 'add',
                                                                'resource' => 'Product\Controller\Product',
                                                                'privilege' => 'add',
                                                                'visible' => false 
                                                        ),
                                                        array (
                                                                'label' => 'Edit Product',
                                                                'route' => 'product/wildcard',
                                                                'controller' => 'product',
                                                                'action' => 'edit',
                                                                'resource' => 'Product\Controller\Product',
                                                                'privilege' => 'edit',
                                                                'visible' => false 
                                                        ) 
                                                ) 
                                        ),
                                        array (
                                                'label' => 'Categories',
                                                'route' => 'product',
                                                'controller' => 'category',
                                                'action' => 'index',
                                                'resource' => 'Product\Controller\Category',
                                                'privilege' => 'index',
                                                'pages' => array (
                                                        array (
                                                                'label' => 'Add Category',
                                                                'route' => 'product',
                                                                'controller' => 'category',
                                                                'action' => 'add',
                                                                'resource' => 'Product\Controller\Category',
                                                                'privilege' => 'add',
                                                                'visible' => false 
                                                        ),
                                                        array (
                                                                'label' => 'Edit Category',
                                                                'route' => 'product/wildcard',
                                                                'controller' => 'category',
                                                                'action' => 'edit',
                                                                'resource' => 'Product\Controller\Category',
                                                                'privilege' => 'edit',
                                                                'visible' => false 
                                                        ) 
                                                ) 
                                        ),
                                        array (
                                                'label' => 'Sub Categories',
                                                'route' => 'product',
                                                'controller' => 'sub-category',
                                                'action' => 'index',
                                                'resource' => 'Product\Controller\SubCategory',
                                                'privilege' => 'index',
                                                'pages' => array (
                                                        array (
                                                                'label' => 'Add Sub Category',
                                                                'route' => 'product',
                                                                'controller' => 'sub-category',
                                                                'action' => 'add',
                                                                'resource' => 'Product\Controller\SubCategory',
                                                                'privilege' => 'add',
                                                                'visible' => false 
                                                        ),
                                                        array (
                                                                'label' => 'Edit Sub Category',
                                                                'route' => 'product/wildcard',
                                                                'controller' => 'sub-category',
                                                                'action' => 'edit',
                                                                'resource' => 'Product\Controller\SubCategory',
                                                                'privilege' => 'edit',
                                                                'visible' => false 
                                                        ) 
                                                ) 
                                        ),
                                        array (
                                                'label' => 'Departments',
                                                'route' => 'product',
                                                'controller' => 'department',
                                                'action' => 'index',
                                                'resource' => 'Product\Controller\Department',
                                                'privilege' => 'index',
                                                'pages' => array (
                                                        array (
                                                                'label' => 'Add Department',
                                                                'route' => 'product',
                                                                'controller' => 'department',
                                                                'action' => 'add',
                                                                'resource' => 'Product\Controller\Department',
                                                                'privilege' => 'add',
                                                                'visible' => false 
                                                        ),
                                                        array (
                                                                'label' => 'Edit Department',
                                                                'route' => 'product/wildcard',
                                                                'controller' => 'department',
                                                                'action' => 'edit',
                                                                'resource' => 'Product\Controller\Department',
                                                                'privilege' => 'edit',
                                                                'visible' => false 
                                                        ) 
                                                ) 
                                        ) 
                                ) 
                        ),
                        array (
                                'label' => 'Product Pricing',
                                'route' => 'product',
                                'controller' => 'pricing',
                                'action' => 'index',
                                'resource' => 'Product\Controller\Pricing',
                                'privilege' => 'index',
                                'pages' => array (
                                        array (
                                                'label' => 'Manage Pricing',
                                                'route' => 'product',
                                                'controller' => 'pricing',
                                                'action' => 'add',
                                                'resource' => 'Product\Controller\Pricing',
                                                'privilege' => 'add',
                                                'visible' => false 
                                        ),
                                        array (
                                                'label' => 'Edit Pricing',
                                                'route' => 'product/wildcard',
                                                'controller' => 'pricing',
                                                'action' => 'edit',
                                                'resource' => 'Product\Controller\Pricing',
                                                'privilege' => 'edit',
                                                'visible' => false 
                                        ) 
                                ) 
                        ) 
                ) 
        ) 
);