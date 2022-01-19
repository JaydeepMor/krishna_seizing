<?php

function removeSpaces(string $string)
{
    return preg_replace('/\s+/', '', $string);
}

function objectToArray(object $obj, bool $deep = true)
{
    $reflectionClass = new \ReflectionClass(get_class($obj));
    $array           = [];

    foreach ($reflectionClass->getProperties() as $property) {
        $property->setAccessible(true);
        $val = $property->getValue($obj);

        if (true === $deep && is_object($val)) {
            $val = objectToArray($val);
        }

        $array[$property->getName()] = $val;
        $property->setAccessible(false);
    }

    return $array;
}

function removeHttp($url)
{
    $disallowed = array('http:', 'https:');

    foreach ($disallowed as $d) {
        if (strpos($url, $d) === 0) {
            return str_replace($d, '', $url);
        }
    }

    return $url;
}

function randomNumber($digits = 4)
{
    return rand(pow(10, $digits-1), pow(10, $digits)-1);
}

function emailPlusAddressing(string $emailId)
{
    $exploded = explode("@", $emailId);

    if (!empty($exploded[0])) {
        $emailId = $exploded[0] . "+D" . randomNumber() . "@" . (!empty($exploded[1]) ? $exploded[1] : "");
    }

    return $emailId;
}

function imeiPlusAddressing(string $imeiNumber)
{
    if (!empty($imeiNumber)) {
        $imeiNumber = $imeiNumber . "+D" . randomNumber();
    }

    return $imeiNumber;
}
