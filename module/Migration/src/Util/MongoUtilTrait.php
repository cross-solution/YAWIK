<?php

declare(strict_types=1);

namespace Yawik\Migration\Util;


trait MongoUtilTrait
{
    /**
     * Get value from namespaced key
     *
     * @param string $nsKey
     * @param array $subject
     * @return mixed
     */
    protected function getNamespacedValue(string $nsKey, array $subject)
    {
        $exp = explode('.', $nsKey);

        $value = null;
        $cSubject = $subject;
        foreach($exp as $name){
            if(array_key_exists($name, $cSubject)){
                $value = $cSubject[$name];
                $cSubject = $cSubject[$name];
            }
        }

        return $value;
    }
}