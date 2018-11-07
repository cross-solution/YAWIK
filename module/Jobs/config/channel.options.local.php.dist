<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
/*
 * here you can add your channels
 */

$channel['yawik'] = array(
                'label' => 'YAWIK',
                'prices' => [ 'base' => 99, 'list' => 99, 'min'  => 99, ],
                'headline' => /*@translate*/ 'publish your job on yawik.org for free',
                'description' => /*@translate*/ 'publish the job for 30 days on %s',
                'linktext' => /*@translate*/ 'yawik.org',
                'route' => 'lang/content',
                'publishDuration' => 60,
                'params' => array(
                    'view' => 'jobs-publish-on-yawik'
                )
            );

$channel['jobsintown'] = array(
                'label' => 'Jobsintown',
                'prices' => [ 'base' => 650, 'list' => 698, 'min'  => 499, ],
                'headline' => '30 Tage, incl. Karrierenetzwerk',
                'description' => 'publish the job for 30 days on %s',
                'linktext' => 'www.jobsintown.de',
                'logo' => 'modules/Jobs/images/channels/jobsintown.png',
                'route' => 'lang/content',
                'publishDuration' => 30,
                'params' => array(
                    'view' => 'jobs-publish-on-jobsintown'
                )
            );

$channel['fazjob'] = array(
                'label' => 'FAZjob.NET',
                'prices' => [ 'base' => 1095, 'list' => 1095, 'min'  => 1095, ],
                'headline' => '30 Tage auf dem Karriereportal der FAZ',
                'description' => 'publish the job for 30 days on %s',
                'linktext' => 'FAZjob.net',
                'logo' => 'modules/Jobs/images/channels/fazjob_net.png',
                'route' => 'lang/content',
                'publishDuration' => 60,
                'params' => array(
                    'view' => 'jobs-publish-on-fazjob-net'
                )
            );

$channel['homepage'] = array(
                'label' => /*@translate*/ 'Your Homepage',
                'prices' => [ 'base' => 0, 'list' => 0, 'min'  => 0, ],
                'headline' => /*@translate*/ 'enable integration of this job on your Homepage',
                'description' => /*@translate*/ 'enable %s of this job on your Homepage',
                'linktext' => /*@translate*/ 'integration',
                'route' => 'lang/content',
                'params' => array(
                    'view' => 'jobs-publish-on-homepage'
                )
            );

return array('multiposting'=> array('channels' => $channel));