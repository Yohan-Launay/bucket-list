<?php

namespace App\Service;

class CensuratorService
{

    public function purify(string $text): string
    {
        $unwantedWords = ["con", "batard", "connard", "fils de pute"];
        foreach($unwantedWords as $unwantedWord) {
            $replacement = str_repeat("*", mb_strlen($unwantedWord));
            $text = str_ireplace($unwantedWord, $replacement, $text);
        }
        return $text;
    }
}