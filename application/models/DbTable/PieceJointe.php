<?php

class Model_DbTable_PieceJointe extends Zend_Db_Table_Abstract
{
    protected $_name="piecejointe"; // Nom de la base
    protected $_primary = "ID_PIECEJOINTE"; // Cl� primaire

    public function affichagePieceJointe($table, $champ, $identifiant)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['pj' => 'piecejointe'])
            ->join($table, "pj.ID_PIECEJOINTE = {$table}.ID_PIECEJOINTE")
            ->joinLeft(['pjs' => 'piecejointestatut'], 'pj.ID_PIECEJOINTESTATUT = pjs.ID_PIECEJOINTESTATUT', ['NOM_STATUT'])
            ->where($champ.' = '.$identifiant)
            ->order('pj.ID_PIECEJOINTE DESC')
        ;

        return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
    }

    public function maxPieceJointe()
    {
        $select = 'SELECT MAX(ID_PIECEJOINTE)
        FROM piecejointe
        ;';

        return $this->getAdapter()->fetchRow($select);
    }

    public function updatePlatauStatus(int $id, string $status): void
    {
        $modelPjStatus = new Model_DbTable_PieceJointeStatut();

        $idStatus = $modelPjStatus->fetchRow(
            $modelPjStatus->select()
                ->from('piecejointestatut')
                ->where('NOM_STATUT = ?', $status)
        )['ID_PIECEJOINTESTATUT'];

        $this->update(['ID_PIECEJOINTESTATUT' => $idStatus], "ID_PIECEJOINTE = {$id}");
    }
}
