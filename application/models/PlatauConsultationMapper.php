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
            'statutAvis' => $consultation->getStatutAvis(),
            'dateAvis' => $consultation->getDateAvis(),
            'statutPec' => $consultation->getStatutPec(),
            'datePec' => $consultation->getDatePec(),
        ];

        if (null === ($id = $consultation->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, ['id = ?' => $id]);
        }
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
        $consultation->setId($row->ID_PLATAU)
            ->setStatutAvis($row->STATUT_AVIS)
            ->setDateAvis($row->DATE_AVIS)
            ->setStatutPec($row->STATUT_PEC)
            ->setDatePec($row->DATE_PEC)
        ;

        return $consultation;
    }

    // TODO A voir si utile, à priori je pense pas
    public function fetchAll(): array
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $consultations = [];

        foreach ($resultSet as $row) {
            $consultation = new Model_PlatauConsultation();
            $consultation->setId($row->id)
                ->setStatutAvis($row->STATUT_AVIS)
                ->setDateAvis($row->DATE_AVIS)
                ->setStatutPec($row->STATUT_PEC)
                ->setDatePec($row->DATE_PEC)
            ;

            $consultations[] = $consultation;
        }

        return $consultations;
    }
}
