<?php

    function processHash($hash) {
        return strtolower(strrev($hash));
    }

    function filterHash($hash) {

        $targetLength = 16;

        $hashCharacters = str_split($hash);
        $finalHash = '';

        foreach ($hashCharacters as $character) {
            if (ctype_alnum($character)) {
                $finalHash = $finalHash . $character;
                if (strlen($finalHash) == $targetLength) break; 
            }
        }

        return $finalHash;

    }

    function createEventHash($name, $description, $creatorKey, $location) {

        $bigString = $name . $description . $creatorKey . $location . time();
        $rawHash = password_hash($bigString, PASSWORD_BCRYPT);
        
        return filterHash(processHash($rawHash));

    }

    function createTimeSlotHash($startDate, $endDate, $eventKey) {

        $bigString = $startDate . $endDate . $eventKey . time();
        $rawHash = password_hash($bigString, PASSWORD_BCRYPT);
        
        return filterHash(processHash($rawHash));

    }
    
?>