<?php

use Sabre\VObject;

class Api_Service_Calendar
{

    public function sync($userid, $commission = null) 
    {

        $request = $this->createRequestForWebcalEvent($userid, 
                                                      $commission);

        $dbDateCommission = new Model_DbTable_DateCommission;

        $calendar = new VObject\Component\VCalendar(array(
            "NAME" => "Calendrier Prévarisc",
            "X-WR-CALNAME" => "Calendrier Prévarisc",
            "REFRESH-INTERVAL;VALUE=DURATION" => "PT5M",
            "X-PUBLISHED-TTL" => "PT5M"
        ));

        $vtimezone = $this->getVTimezoneComponent($calendar);
        $calendar->add($vtimezone);


        foreach ($dbDateCommission->getEventInCommission($request) as $commissionEvent) {
            $event = $this->createICSEvent($commissionEvent);
            if ($event) {
                $calendar->add("VEVENT", $event);    
            }
        }

        echo $calendar->serialize();
    }

    private function getVTimezoneComponent($calendar)
    {
        $vtimezone = new VObject\Component\VTimeZone($calendar, "VTIMEZONE");
        $daylight = new VObject\Component($calendar, "DAYLIGHT", [
                "DTSTART" => new DateTime("16010325T020000"),
                "RRULE" => "FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3",
                "TZOFFSETFROM" => "+0100",
                "TZOFFSETTO" => "+0200"
            ]);
        $standard = new VObject\Component($calendar, "STANDARD", [
                "DTSTART" => new DateTime("16011028T030000"),
                "RRULE" => "FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10",
                "TZOFFSETFROM" => "+0200",
                "TZOFFSETTO" => "+0100"
            ]);
        $vtimezone->TZID = date_default_timezone_get(); 
        $vtimezone->add($standard);
        $vtimezone->add($daylight); 

        return $vtimezone;
    }

    /**
     * [createRequestForWebcalEvent description]
     * @return string La requête générée
     */
    private function createRequestForWebcalEvent($userid, $commission)
    {
        $request = "ID_UTILISATEUR = " . $userid;

        // Gestion des commissions concernés
        if ($commission) {
            $request .= " AND COMMISSION_CONCERNE = " . $commission;
        } 
        
        // Gestion de la date (un an avant l'année actuelle)
        $today = new \DateTime();
        $yearBefore = $today->modify("-1 year")->format("Y");
        $request .= " AND YEAR(DATE_COMMISSION) >= '" . $yearBefore . "'";

        return $request;
    }


    private function createICSEvent($commissionEvent)
    {
        $event = null;

        if (is_array($commissionEvent)) {
            $etsService = new Service_Etablissement;
            $ets = $etsService->get($commissionEvent["ID_ETABLISSEMENT"]);
            // Cas d'une commission en salle
            if ($commissionEvent["ID_COMMISSIONTYPEEVENEMENT"] === 1) {
                $summary = sprintf("%s %s - %s",
                                $commissionEvent["LIBELLE_DOSSIERTYPE"],
                                $commissionEvent["LIBELLE_DOSSIERNATURE"],
                                $commissionEvent["OBJET_DOSSIER"]);
                $geo = $commissionEvent["LIBELLE_COMMISSION"];
            // Cas d'une visite d'une commission ou d'un groupe de visite
            } else {
                $adresse = count($ets["adresses"]) > 0 ? $ets["adresses"][0] : null;
                $summary = $commissionEvent["LIBELLE_DATECOMMISSION"];
                $geo = $ets["informations"]["LIBELLE_ETABLISSEMENTINFORMATIONS"];
                if ($adresse) {
                    $geo .= sprintf(" %s %s %s %s %s",
                        $adresse["NUMERO_ADRESSE"],
                        $adresse["LIBELLE_RUETYPE"],
                        $adresse["LIBELLE_RUE"],
                        $adresse["CODEPOSTAL_COMMUNE"],
                        $adresse["LIBELLE_COMMUNE"]
                    );
                }
            }
            $dateStartHour = $commissionEvent["HEURE_DEB_AFFECT"] ? 
                                "HEURE_DEB_AFFECT" : "HEUREDEB_COMMISSION";
            $dateEndHour = $commissionEvent["HEURE_FIN_AFFECT"] ? 
                                "HEURE_FIN_AFFECT" : "HEUREFIN_COMMISSION";
            $dtStart = new \DateTime(sprintf("%s %s", 
                        $commissionEvent["DATE_COMMISSION"],
                        $commissionEvent[$dateStartHour]),
                        new DateTimeZone(date_default_timezone_get()));

            $dtEnd = new \DateTime(sprintf("%s %s", 
                        $commissionEvent["DATE_COMMISSION"],
                        $commissionEvent[$dateEndHour]),
                        new DateTimeZone(date_default_timezone_get()));

            $event = array(
                "SUMMARY"       => $summary,
                "LOCATION"      => $geo,
                "DESCRIPTION"   => $this->getEventCorps($commissionEvent, $ets),
                "DTSTART"       => $dtStart,
                "DTEND"         => $dtEnd
            );
            
        }

        return $event;
    }

    private function getAvisEtablissement($event, $ets)
    {
        $servEtab = new Service_Etablissement;
        $avisDoss = $servEtab->getAvisEtablissement(
            $ets['general']['ID_ETABLISSEMENT'], 
            $ets['general']['ID_DOSSIER_DONNANT_AVIS']
        );
        if ($ets['presence_avis_differe'] && $avisDoss === "avisDiff") {
            $avis = "Dossier avec avis differé";
        } elseif ($ets['avis'] === 1) {
            $avis = "Favorable";
            if ($ets['informations']["ID_GENRE"] === 3) {
                $avis .= " à l'exploitation";
            }
        } elseif ($ets['avis'] === 2) {
            $avis = "Défavorable";
            if ($ets['informations']["ID_GENRE"] === 3) {
                $avis .= " à l'exploitation";
            }
        } else {
            $avis = "Avis d'exploitation indisponible";
        }
        
        return $avis;
    }


    private function getEventCorps($commissionEvent, $ets)
    {
        $corpus = "Coordonnées des participants :\r\n";

        $dossierService = new Service_Dossier;
        $contacts = $dossierService->getAllContacts(
            $commissionEvent["ID_DOSSIER"]);
        foreach ($contacts as $contact) {
            $corpus .= $this->formatUtilisateurInformations($contact);
        }
        $corpus .= "\n";

        $adresseService = new Service_Adresse;
        $maire = $adresseService->getMaire($commissionEvent["NUMINSEE_COMMUNE"]);
        $corpus .= sprintf("Coordonnées de la mairie :\r\n%s\r\n\r\n",
                    $this->formatUtilisateurInformations($maire));
        
        if ($commissionEvent["ID_DOSSIERTYPE"] === 1) {
            if ($commissionEvent["TYPESERVINSTRUC_DOSSIER"] === "servInstCommune") {
                $serviceInstruct = $maire;
            } else {
                $dbGroupement = new Model_DbTable_Groupement;
                $serviceInstruct = $dbGroupement->getByLibelle(
                            $commissionEvent["SERVICEINSTRUC_DOSSIER"]);
                $serviceInstruct = count($serviceInstruct) > 0 ?
                                    $serviceInstruct[0] : null;
            }
            $corpus .= sprintf("Coordonnées du service instructeur :\n%s\n\n",
                        $this->formatUtilisateurInformations($serviceInstruct));
        }
        
        $corpus .= sprintf("Date de la dernière visite périodique : %s\n\n",
                            $ets["last_visite"]);

        $corpus .= sprintf("Avis d'exploitation de l'établissement : %s\n\n",
                            $this->getAvisEtablissement($commissionEvent, $ets));

        return $corpus;
    }

    private function formatUtilisateurInformations($user) 
    {
        $str = "";
        if ($user && is_array($user)) {
            if ($user["NOM_UTILISATEURINFORMATIONS"] 
                    && $user["PRENOM_UTILISATEURINFORMATIONS"]) {
                $str .= sprintf("- %s %s, ",
                        $user["NOM_UTILISATEURINFORMATIONS"],
                        $user["PRENOM_UTILISATEURINFORMATIONS"]);
                if ($user["NUMEROADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["RUEADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["NUMEROADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["CPADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["VILLEADRESSE_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf("%s %s %s %s, ",
                                $user["NUMEROADRESSE_UTILISATEURINFORMATIONS"],
                                $user["RUEADRESSE_UTILISATEURINFORMATIONS"],
                                $user["CPADRESSE_UTILISATEURINFORMATIONS"],
                                $user["VILLEADRESSE_UTILISATEURINFORMATIONS"]
                            );
                }
                if ($user["TELFIXE_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf("%s, ", 
                        $user["TELFIXE_UTILISATEURINFORMATIONS"]);
                }
                if ($user["TELFAX_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf("%s, ", 
                        $user["TELFAX_UTILISATEURINFORMATIONS"]);
                }
                if ($user["MAIL_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf("%s, ", 
                        $user["MAIL_UTILISATEURINFORMATIONS"]);
                }
                $str .= "\n";
            }
        }

        return $str;
        
    }
}