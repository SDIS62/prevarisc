SET NAMES 'utf8';

CREATE TABLE `platauconsultation` (
    `ID_PLATAU` char(11) NOT NULL,
    `STATUT_AVIS` varchar(50),
    `DATE_AVIS` date,
    `STATUT_PEC` varchar(50),
    `DATE_PEC` date,
    PRIMARY KEY (`ID_PLATAU`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
