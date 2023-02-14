<?php

class Service_PieceJointe
{
    public function exportPlatau(array $pjs): void
    {
        $modelPj = new Model_DbTable_PieceJointe();

        array_map(function ($idPj) use ($modelPj) {
            $modelPj->updatePlatauStatus($idPj, 'to_be_exported');
        }, array_keys($pjs));
    }
}
