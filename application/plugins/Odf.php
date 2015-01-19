<?php


class Plugin_Odf extends Odf
{
    /**
     * Surcharge de la méthode standard setSegment pour modifier l'expression réglière
     * qui permet de détecter les boucles : on ajoute la possibilité d'avoir plusieurs
     * fois la même boucle dans un document
     */
    public function setSegment($segment)
    {
        if (array_key_exists($segment, $this->segments)) {
            return $this->segments[$segment];
        }
        // $reg = "#\[!--\sBEGIN\s$segment\s--\](.*)\[!--\sEND\s$segment\s--\]#sm";
        $reg = "#\[!--\sBEGIN\s$segment\s--\]([^\[!--]*)\[!--\sEND\s$segment\s--\]#sm";
        if (preg_match($reg, html_entity_decode($this->contentXml), $m) == 0) {
            throw new OdfException("'$segment' segment not found in the document");
        }
        $this->segments[$segment] = new Segment($segment, $m[1], $this);
        return $this->segments[$segment];
    }
}
