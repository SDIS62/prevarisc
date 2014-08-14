<?php

interface Service_Interface_TexteApplicable
{
    public function getAllTextesApplicables($id);
    public function saveTextesApplicables($id_etablissement, array $textes_applicables);
}
