<?php

use Sabre\VObject;

class Api_Service_Calendar
{

    const LF = "\r\n";
    
    public function sync($userid, $commission = null) 
    {

        $dossierEvent = $this->createRequestForWebcalEvent($userid, 
                                                           $commission);

        $calendrierNom = "Prévarisc";
        if ($commission) {
            $dbCommission = new Model_DbTable_Commission;
            $resultLibelle = $dbCommission->getLibelleCommissions($commission);
            if (count($resultLibelle) > 0) {
                $calendrierNom .= " " . $resultLibelle[0]['LIBELLE_COMMISSION']; 
            }
        }

        // Le refresh est par défaut à 5 minutes
        $refreshTime = (getenv('PREVARISC_CALENDAR_REFRESH_TIME') 
                        && getenv('PREVARISC_CALENDAR_REFRESH_TIME') !== '') ? 
                        getenv('PREVARISC_CALENDAR_REFRESH_TIME') : 'PT5M';

        $calendar = new VObject\Component\VCalendar(array(
            "NAME" => $calendrierNom,
            "X-WR-CALNAME" => $calendrierNom,
            "REFRESH-INTERVAL;VALUE=DURATION" => $refreshTime,
            "X-PUBLISHED-TTL" => $refreshTime
        ));

        $vtimezone = $this->getVTimezoneComponent($calendar);
        $calendar->add($vtimezone);

        foreach ($dossierEvent as $commissionEvent) {
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
        $today = new \DateTime();
        $yearBefore = $today->modify("-1 year")->format("Y");

        $dbDateCommission = new Model_DbTable_DateCommission;

        return $dbDateCommission->getEventInCommission($userid, $commission, $yearBefore);
    }


    private function createICSEvent($commissionEvent)
    {
        $event = null;

        if (is_array($commissionEvent)) {
            $etsService = new Service_Etablissement;
            $ets = $etsService->get($commissionEvent["ID_ETABLISSEMENT"]);

            $etsLibelleArray = array();
            foreach($ets['parents'] as $parent) {
                $etsLibelleArray[] = trim($parent['LIBELLE_ETABLISSEMENTINFORMATIONS']);
            }
            
            $etsLibelleArray[] = trim($ets['informations']['LIBELLE_ETABLISSEMENTINFORMATIONS']);
            $etsLibelle = implode(" - ", $etsLibelleArray);
            $commune = count($ets["adresses"]) > 0 ? $ets["adresses"][0]["LIBELLE_COMMUNE"] : '';
            // Cas d'une commission en salle
            if ($commissionEvent["ID_COMMISSIONTYPEEVENEMENT"] === 1) {
                if ($commissionEvent["TYPE_DOSSIER"] === 3) {
                    $libelleSum = $commissionEvent["LIBELLE_DATECOMMISSION"];
                } else {
                    $libelleSum = $commissionEvent["OBJET_DOSSIER"];
                }
                $summary = sprintf("#%s %s (%s) : %s %s - %s",
                                $ets['general']['NUMEROID_ETABLISSEMENT'],
                                $etsLibelle,
                                $commune,
                                $commissionEvent["LIBELLE_DOSSIERTYPE"],
                                $commissionEvent["LIBELLE_DOSSIERNATURE"],
                                trim($libelleSum));
                $geo = sprintf("Commission en salle de %s", $commissionEvent["LIBELLE_COMMISSION"]);
            // Cas d'une visite d'une commission ou d'un groupe de visite
            } else {
                $summary = sprintf('#%s %s : %s', 
                        $ets['general']['NUMEROID_ETABLISSEMENT'], 
                        $etsLibelle,
                        $commissionEvent["LIBELLE_DATECOMMISSION"]
                        );
                $adresse = count($ets["adresses"]) > 0 ? $ets["adresses"][0] : null;
                if ($adresse) {
                    $geo = sprintf("%s %s %s, %s %s",
                        $adresse["NUMERO_ADRESSE"],
                        $adresse["LIBELLE_RUETYPE"],
                        $adresse["LIBELLE_RUE"],
                        $adresse["CODEPOSTAL_COMMUNE"],
                        $adresse["LIBELLE_COMMUNE"]
                    );
                } else {
                    $geo = '';
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
                "SUMMARY"       => substr($summary, 0, 255),
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
        $corpus = "Contacts du dossier :".self::LF;

        $dossierService = new Service_Dossier;
        $servEtab = new Service_Etablissement;
        $contactsDossier = $dossierService->getAllContacts(
            $commissionEvent["ID_DOSSIER"]);
        $contactsEts = $servEtab->getAllContacts($ets['general']['ID_ETABLISSEMENT']);
        $contacts = array_merge($contactsDossier, $contactsEts);
        if (count($contacts) > 0) {
            foreach ($contacts as $contact) {
                $corpus .= $this->formatUtilisateurInformations($contact);
            }    
        } else {
            $corpus .= "Aucun contact".self::LF;
        }
        
        $corpus .= self::LF.self::LF;

        $adresseService = new Service_Adresse;
        $maire = $adresseService->getMaire($commissionEvent["NUMINSEE_COMMUNE"]);
        if ($maire && count($maire) > 0) {
            $corpus .= sprintf("Coordonnées de la mairie :%s%s%s",
                    self::LF,
                    $this->formatUtilisateurInformations($maire),
                    self::LF.self::LF
                    );
        } else {
             $corpus .= "Aucune coordonées pour la mairie.".self::LF.self::LF;
        }
        
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
            if ($maire && count($maire) > 0) {
                $corpus .= sprintf("Coordonnées du service instructeur :%s%s%s",
                        self::LF,
                        $this->formatUtilisateurInformations($serviceInstruct)
                        .self::LF.self::LF
                        );
            } else {
                $corpus .= "Aucune coordonées pour le service instructeur.".self::LF.self::LF;
            }
        }
        
        $lastVisitestr = $ets["last_visite"] ? : 'Aucune date.';
        $corpus .= sprintf("Date de la dernière visite périodique : %s%s",
                           $lastVisitestr,
                           self::LF.self::LF
                );

        $corpus .= sprintf("Avis d'exploitation de l'établissement : %s",
                            $this->getAvisEtablissement($commissionEvent, $ets));

        return $corpus;
    }

    private function formatUtilisateurInformations($user) 
    {
        $str = "";
        
        if ($user && is_array($user)) {
            if ($user["NOM_UTILISATEURINFORMATIONS"]) {
                $str .= sprintf("- %s : %s %s",
                        $user["LIBELLE_FONCTION"],
                        $user["NOM_UTILISATEURINFORMATIONS"],
                        $user["PRENOM_UTILISATEURINFORMATIONS"]);
                if ($user["NUMEROADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["RUEADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["NUMEROADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["CPADRESSE_UTILISATEURINFORMATIONS"]
                        && $user["VILLEADRESSE_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf(", %s %s, %s %s",
                                $user["NUMEROADRESSE_UTILISATEURINFORMATIONS"],
                                $user["RUEADRESSE_UTILISATEURINFORMATIONS"],
                                $user["CPADRESSE_UTILISATEURINFORMATIONS"],
                                $user["VILLEADRESSE_UTILISATEURINFORMATIONS"]
                            );
                }
                if ($user["TELFIXE_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf(", %s", 
                        $user["TELFIXE_UTILISATEURINFORMATIONS"]);
                }
                if ($user["TELFAX_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf(", %s", 
                        $user["TELFAX_UTILISATEURINFORMATIONS"]);
                }
                if ($user["MAIL_UTILISATEURINFORMATIONS"]) {
                    $str .= sprintf(", %s", 
                        $user["MAIL_UTILISATEURINFORMATIONS"]);
                }
                $str .= "\n";
            }
        }

        return $str;
        
    }
}