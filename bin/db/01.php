<?php

include_once 'connect_mongo.php';

$c = new connect_mongo();
$db = $c->connect();

$jobs = $db->jobs;
$applications = $db->applications;
$files = $db->selectCollection('applications.files');
$users = $db->selectCollection('users');

var_dump($db->getCollectionNames());

if (True) {
    // 01 
    // changing Job Job-ID
    $cursor = $jobs->find(array("userId"=>array('$exists'=>1)));
    // $cursor->sort(array("a" => 1))
    //$cursor->limit(1);

    var_dump(iterator_to_array($cursor));

    $array=iterator_to_array($cursor);

    // 
    //$r=$collection->find($filter,   // Filter
    //                                array("name"=>1,"loc"=>1, "area"=>1, "admin1 name"=>1, "country code" => 1, "feature code" => 1))   // Felder
    //                            ->sort(array("population"=>-1))  // Reihenfolge
    //                            ->limit($limit);     
    // new MongoRegex("/^" . $item . "/");
    //
    //$jobs->update($criteria, $newObject  , /*upsert*/true );
    //

    foreach ($cursor as $key => $value) {
        var_dump($key);
        $jobs->update(array("_id"=> new MongoId($key)),array('$unset' => array('userId'=>1), '$set'=>array('user'=> new MongoId($value['userId']))));
    }

    //$jobs->update(array("_id"=>MongoId()
}


if (True) {
    // changing Application Job-ID
    $cursor = $applications->find(array("jobId"=>array('$exists'=>1)));
    foreach ($cursor as $key => $value) {
        var_dump($key);
        try {
            $applications->update(array("_id"=> new MongoId($key)),array('$set'=>array('job'=> new MongoId($value['jobId']))));
        } catch (Exception $e) {
        }
    }
}

if (True) {
    // changing Application refs
    // "refs" : {
    //      "applications-files" : [
    //        "52a84cad5246e17429000001",...]
    //  }
    //$cursor = $applications->find(array('refs'=>array('applications-files' => array('$exists'=>1))));
    $cursor = $applications->find(array('refs.applications-files' => array('$exists' => 1)), array('refs.applications-files', 'attachments'));
    foreach ($cursor as $key => $value) {
        var_dump($value);
        $new_values = array();
        foreach ($value['refs']['applications-files'] as $value_af) {
            var_dump($key . '-' . $value_af);
            $new_values[] = new MongoId($value_af);
        }
        try {
            $applications->update(array("_id" => new MongoId($key)), array('$set' => array("attachments" => $new_values)));
        } catch (Exception $e) {
            
        }
    }
}


if (True) {
    // changing Application refs
    // "refs" : {
    //      "applications-files" : [
    //        "52a84cad5246e17429000001",...]
    //  }, array('filename')
    //$cursor = $applications->find(array('refs'=>array('applications-files' => array('$exists'=>1))));
    $cursor = $files->find(array('$and' => array( array('name' => array('$exists' => 0)), array('filename' => array('$exists' => 1)))), array('filename'));
    foreach ($cursor as $key => $value) {
        var_dump($value);
        $files->update(array("_id" => new MongoId($key)), array('$set' => array("name" => $value['filename'])));
    }
}


if (True) {
    // changing Settings to new format.
    
    $cursor = $users->find(array('settings' => array('$exists' => 1)), array('settings'));
    //$cursor = $users->find();
    foreach ($cursor as $key => $value) {
        $isSettingsOld = False;
        foreach ($value['settings'] as $settingKey => $setting) {
            var_dump($settingKey);
            $isSettingsOld |= !is_numeric($settingKey);
        }
        if ($isSettingsOld) {
            $users->update(array("_id" => new MongoId($key)), array('$set' => array("settings_deprecated" => $value['settings'])));
        }
    }
    
    $cursor = $users->find(array('settings_deprecated' => array('$exists' => 1)), array('settings_deprecated'));
    foreach ($cursor as $key => $value) {
        var_dump($key);
        $newSettings = array();
        //var_dump($value['settings_deprecated']);
        if (!empty($value['settings_deprecated'])) {
            foreach($value['settings_deprecated'] as $settingsKey => $settingsValue) {
                $newSetting = array();
                switch ($settingsKey) {
                    case 'applications':
                        $newSetting = array(
                            '_entity' => "Applications\\Entity\\Settings",
                            '_module' => 'Applications',
                        );
                        foreach ($settingsValue as $setKey => $setVal) {
                            $newSetting[$setKey] = $setVal;
                        }
                        break;
                        
                    case 'settings':
                        $newSetting = array(
                            '_entity' => 'Core\\Entity\\SettingsContainer',
                            '_module' => 'Core',
                            'localization' => array('language' => $settingsValue['language'])
                        );
                        break;
                        
                    default:
                
                        $newSetting[] = array(
                            "_entity" => "Settings\Entity\ModuleSettingsContainer",
                            "settings" => $settingsValue,
                            "module" => ucfirst(strtolower($settingsKey)),
                        );
                        break;
                }
                $newSettings[] = $newSetting;
            }
            $users->update(array("_id" => new MongoId($key)), array('$set' => array("settings" => $newSettings)));
            //var_dump($newSettings);
        }
    }
    
}


if (True) {
    // changing Application Attachements, the way how ownership is stored
    
    $cursor = $files->find(array('$and' => array( 
        array('allowedUserIds' => array('$exists' => 1)), 
        array('allowedUsers' => array('$exists' => 0)))), array('allowedUserIds'));
    
    foreach ($cursor as $key => $value) {
        $arrayOfMongoIds = array();
        foreach ($value['allowedUserIds'] as $allowedUserId) {
            //var_dump($allowedUserId);
            if (!empty($allowedUserId)) {
                $arrayOfMongoIds[] = new MongoId($allowedUserId);
            }
        }
        if (empty($arrayOfMongoIds)) {
            // if no one has access to an attachment, at least grand the owner an access
            $arrayOfMongoIds[] = new MongoId($key);
        }
        //var_dump($key);
        //var_dump($arrayOfMongoIds);
        $files->update(array("_id" => new MongoId($key)), array('$set' => array("allowedUsers" => $arrayOfMongoIds)));
        
    }
}

if (True) {
    // granding every citizen from AMS an secret key
    $cursor = $users->find(array('$and' => array(
        array('secret' => array('$exists' => 0)),
        array('login' => array('$exists' => 1))
        )), array('login', 'credential'));
    foreach ($cursor as $key => $value) {
        $login = $value['login'];
        if (preg_match('/^.*@ams$/', $login)) {
            echo "add secret to " . $login;
            $users->update(array("_id" => new MongoId($key)), array('$set' => array("secret" => $value['credential'])));
        }
    }
}

//$files->update(array("_id" => new MongoId("5278bec6ae0259640d000001")), array('$unset' => array("allowedUsers" => "")));
