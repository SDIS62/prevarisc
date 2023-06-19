<?php

class Model_PlatauConsultationMapper
{
    private $dbTable;

    public function setDbTable(string $dbTable)
    {
        $dbTable = new $dbTable();

        if (!$dbTable instanceof Model_DbTable_PlatauConsultation) {
            throw new Exception(sprintf('Incorrect model dbtable. Expecting %s', Model_DbTable_PlatauConsultation::class));
        }

        $this->dbTable = $dbTable;

        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->dbTable) {
            $this->setDbTable(Model_DbTable_PlatauConsultation::class);
        }

        return $this->dbTable;
    }

    public function save(Model_PlatauConsultation $consultation): void
    {
        $data = [
            'STATUT_AVIS' => $consultation->getStatutAvis(),
            'STATUT_PEC' => $consultation->getStatutPec(),
        ];

        $this->getDbTable()->update($data, ['ID_PLATAU = ?' => $consultation->getId()]);
    }

    /**
     * @return null|Model_PlatauConsultation
     */
    public function find(string $id, Model_PlatauConsultation $consultation)
    {
        $result = $this->getDbTable()->find($id);

        if (0 === count($result)) {
            return null;
        }

        $row = $result->current();

        $datePec = null !== $row->DATE_PEC ? (new Zend_Date($row->DATE_PEC, 'yyyy-MM-dd'))
            ->get(Zend_Date::WEEKDAY.' '.Zend_Date::DAY_SHORT.' '.Zend_Date::MONTH_NAME_SHORT.' '.Zend_Date::YEAR, 'fr') : null;
        $dateAvis = null !== $row->DATE_AVIS ? (new Zend_Date($row->DATE_AVIS, 'yyyy-MM-dd'))
            ->get(Zend_Date::WEEKDAY.' '.Zend_Date::DAY_SHORT.' '.Zend_Date::MONTH_NAME_SHORT.' '.Zend_Date::YEAR, 'fr') : null;

        $consultation->setId($row->ID_PLATAU)
            ->setStatutAvis($row->STATUT_AVIS ?? Model_Enum_PlatauStatutAvis::INCONNU)
            ->setDateAvis($dateAvis)
            ->setStatutPec($row->STATUT_PEC ?? Model_Enum_PlatauStatutPec::INCONNU)
            ->setDatePec($datePec)
        ;

        return $consultation;
    }
}
