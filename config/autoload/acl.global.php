<?php
namespace AfcCommons;

return array(
    'acl' => array(
        'enabled' => true,
        'options' => array(
            
            /* What should be the behaviour of if the request resource 
             * is not found when checking in acl
             * 
             * Option Type: String (allow,deny)
             * Default : allow
             */
            'access_when_no_resource_found' => "deny", 
            
            /*
             * Enable row level acl for database records
             * Option Type: Boolean (true,false)
             * Default : false
             * 
             * WARNING : row level acl is expensive as 
             * multiple database query are executed, also these is 
             * effective when using with doctrine
             */
            'db_row_level_acl_enabled' => true,
            
            /*
             * Define the default behavior for records that were not 
             * monitor afccommons before being installed.  
             */
            'authorize_previous_db_records' => false,
            
            /* 
             * Note that the perssion type are inspired from linux permission
             * types. The following stands for permission
             * 7 full (select,update & delete)
             * 6 read and write (select & update)
             * 5 read and execute (select & delete)
             * 4 read only (select)
             * 3 write and execute (update & delete)
             * 2 write only (update)
             * 1 execute only (delete)
             * 0 none 000 (none)
             *             
             * 710 (UGO) stands for following explanation:
             * 
             * (U = 7) => User has full rights on the record he created
             * 
             * (G = 1) => Group in which owner of record belongs has only read
             * access
             * 
             * (O = 0) => Other users not in the group of record owner, has no
             * rights on the record
             * 
             * Option Type : Integer
             * Default : 710 (User has full access and his group member has read access)
             */
            'db_row_default_permission' => array(
                
                // The user that created record can do anything with the record
                'user_access_mode' => 7,
                
                // The user from other groups can read the record
                'group_access_mode' => 7,
                
                // Other users have no access to the record
                'other_access_mode' => 1
            )
        )
    )
);