<?php

    class View_Helper_Dates
    {
        /** 
        * A sweet interval formatting, will use the two biggest interval parts. 
        * On small intervals, you get minutes and seconds. 
        * On big intervals, you get months and days. 
        * Only the two biggest parts are used. 
        * 
        * @param DateTime $start 
        * @param DateTime|null $end 
        * @return string 
        */ 
        public function formatDateDiff($start, $end=null) { 
            if(!($start instanceof DateTime)) { 
                $start = new DateTime($start); 
            } 

            if($end === null) { 
                $end = new DateTime(); 
            } 

            if(!($end instanceof DateTime)) { 
                $end = new DateTime($start); 
            } 

            $interval = $end->diff($start); 
            $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals 

            $format = array(); 
            if($interval->y !== 0) { 
                $format[] = "%y ".$doPlural($interval->y, "année"); 
            } 
            if($interval->m !== 0) { 
                $format[] = "%m mois";
            } 
            if($interval->d !== 0) { 
                $format[] = "%d ".$doPlural($interval->d, "jour"); 
            } 
            if($interval->h !== 0) { 
                $format[] = "%h ".$doPlural($interval->h, "heure"); 
            } 
            if($interval->i !== 0) { 
                $format[] = "%i ".$doPlural($interval->i, "minute"); 
            } 
            if($interval->s !== 0) { 
                if(!count($format)) { 
                    return "<= 1 min"; 
                } else { 
                    $format[] = "%s ".$doPlural($interval->s, "seconde"); 
                } 
            } 

            // We use the two biggest parts 
            $format = array_shift($format); 
            
            // Prepend 'since ' or whatever you like 
            return $interval->format($format); 
        } 
    }