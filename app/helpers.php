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
        $alreadyAddressed = explode("+D", $exploded[0]);

        if (!empty($alreadyAddressed) && count($alreadyAddressed) > 1) {
            $exploded[0] = $alreadyAddressed[0];
        }

        $emailId = $exploded[0] . "+D" . randomNumber() . "@" . (!empty($exploded[1]) ? $exploded[1] : "");
    }

    return $emailId;
}

function imeiPlusAddressing(string $imeiNumber)
{
    if (!empty($imeiNumber)) {
        $alreadyAddressed = explode("+D", $imeiNumber);

        if (!empty($alreadyAddressed) && count($alreadyAddressed) > 1) {
            $imeiNumber = $alreadyAddressed[0];
        }

        $imeiNumber = $imeiNumber . "+D" . randomNumber();
    }

    return $imeiNumber;
}

function reArrengeRegistrationNumber(string $regNo)
{
    if (
        strpos($regNo, '-') !== false || 
        strpos($regNo, '/') !== false || 
        strpos($regNo, '\\') !== false || 
        strpos($regNo, '.') !== false || 
        strpos($regNo, '_') !== false || 
        strpos($regNo, '~') !== false || 
        strpos($regNo, '`') !== false || 
        strpos($regNo, '@') !== false || 
        strpos($regNo, '#') !== false || 
        strpos($regNo, '$') !== false || 
        strpos($regNo, '%') !== false || 
        strpos($regNo, '^') !== false || 
        strpos($regNo, '&') !== false || 
        strpos($regNo, '(') !== false || 
        strpos($regNo, ')') !== false || 
        strpos($regNo, '+') !== false || 
        strpos($regNo, '=') !== false || 
        strpos($regNo, '*') !== false || 
        strpos($regNo, '|') !== false
    ) {
        // return $regNo;
    }

    $regNoTemp   = str_split(str_replace(' ', '', trim($regNo)), 2);

    $regNoNew    = null;

    $isConverted = false;

    if (!empty($regNoTemp[0])) {
        if (!empty($regNoTemp[1])) {
            $regNoNew = $regNoTemp[0] . '-' . $regNoTemp[1];

            $tempRegNo = null;
            foreach ($regNoTemp as $key => $temp) {
                if ($key < 2) {
                    continue;
                }

                $tempRegNo .= $temp;
            }

            $lastNumber   = (!empty($tempRegNo)) ? substr($tempRegNo, -4) : null;
            $seriesNumber = (!empty($lastNumber)) ? implode('', explode($lastNumber, $tempRegNo)) : null;

            if (!empty($seriesNumber)) {
                $regNoNew .= '-' . $seriesNumber;
            }

            if (!empty($lastNumber)) {
                $regNoNew .= '-' . $lastNumber;

                $isConverted = true;
            }
        }
    }

    if (!$isConverted) {
        $regNoNew = $regNo;
    }

    return $regNoNew;
}
