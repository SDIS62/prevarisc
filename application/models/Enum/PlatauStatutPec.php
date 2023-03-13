<?php

class Model_Enum_PlatauStatutPec
{
    public const LABELS = [
        self::INCONNU => null,
        self::EN_ATTENTE => 'En attente',
        self::PRISE_EN_COMPTE => 'Prise en compte',
        self::A_RENVOYER => 'Prise en compte (en attente de renvoi)',
        self::EN_ERREUR => 'En erreur',
    ];

    public const INCONNU = 'unknown';
    public const EN_ATTENTE = 'awaiting';
    public const PRISE_EN_COMPTE = 'taken_into_account';
    public const A_RENVOYER = 'to_export';
    public const EN_ERREUR = 'in_error';

    public function getLabel(string $enumValue): string
    {
        return self::LABELS[$enumValue];
    }
}
