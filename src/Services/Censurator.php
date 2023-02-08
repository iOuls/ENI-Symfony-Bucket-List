<?php

namespace App\Services;

class Censurator
{
    public function purify(string $string)
    {
        // séparation de la phrase
        $stringSplit = explode(' ', $string);
        $resultat = '';
        $file = explode(' ', file_get_contents('../src/Services/words.txt'));

        // parcours le tableau par mots
        foreach ($stringSplit as $word) {

            // compare chaque élément dans le fichier des mots censurés
            foreach ($file as $censuredWord) {
                if ($word == $censuredWord) {
                    $word = str_replace($censuredWord, '*****', $word);
                    $resultat .= $word . ' ';
                } else {
                    $resultat .= $word . ' ';
                }
            }
        }

        // renvoi le résultat en une string avec censure des mots
        return $resultat;
    }
}