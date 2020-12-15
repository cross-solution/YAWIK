<?php

$FACEBOOK_APP_ID        = getenv('FACEBOOK_APP_ID');
$FACEBOOK_APP_SECRET    = getenv('FACEBOOK_APP_SECRET');
$LINKEDIN_APP_ID        = getenv('LINKEDIN_APP_ID');
$LINKEDIN_APP_SECRET    = getenv('LINKEDIN_APP_SECRET');

return array(
	'hybridauth' => array(
        "Facebook" => array ( 
            "enabled" => true,
            "keys"    => array ( "id" => $FACEBOOK_APP_ID, "secret" => $FACEBOOK_APP_SECRET ),
            "scope"   => "email, user_about_me, user_birthday, user_hometown, user_work_history, user_education_history",// optional
            "display" => "popup"

        ),
        "LinkedIn" => array (
            "enabled" => true,
            "keys"    => array ( "id" => $LINKEDIN_APP_ID, "secret" => $LINKEDIN_APP_SECRET ),
            "scope"   => "r_basicprofile,r_emailaddress"
        ),
        "XING" => array(
            "enabled" => false,
            'keys'    => array ( "key" => '###XING APP KEY###', 'secret' => '### XING APP SECRET ###'),
            "scope"   => ''
        ),
        "Github" => array(
            "enabled" => false,
            'keys'    => array ( "id" => '###Your GitHub AppID ###', 'secret' => '###Your GitHub Secret###'),
            "scope"   => ''
        ),
        "Google" => array(
             // see http://hybridauth.sourceforge.net/userguide/IDProvider_info_Google.html
             "enabled" => false,
             'keys'    => array ( "id" => '###Your Google Client-ID ###', 'secret' => '###Your GitHub Secret###'),
             "scope"   => "https://www.googleapis.com/auth/userinfo.profile ". // optional
                          "https://www.googleapis.com/auth/userinfo.email"   , // optional
             "access_type"     => "offline",   // optional
             "approval_prompt" => "force",     // optional
        ),



//        "OpenID" => array(
//            'enabled' => true
//        )
    ),
    
    'Auth' => array(
    	'first_login' => array (
    	    'role' => '%%role%%',                                            // role set on the first login.
    	    'auth_suffix' => '%%auth.suffix%%',                              // an auth suffix is needed, if you plan to add external apps.
    	),
    	// this allows an external application to use the YAWIK API
    	// applications[USERPOSTFIX] => AppKey
        'external_applications' => array(
            '%%external.app.prefix%%' => '%%external.app.key%%',
        ),
    ),
);