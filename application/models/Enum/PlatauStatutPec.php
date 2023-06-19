<?php

class Model_Enum_PlatauStatutPec
{
    public const LABELS = [
        self::INCONNU => null,
        self::EN_ATTENTE => 'En attente de complétude sur Prevarisc',
        self::PRISE_EN_COMPTE => 'Prise en compte sur Plat\'AU',
        self::A_RENVOYER => 'Prise en compte sur Plat\'AU (en attente de renvoi)',
        self::EN_ERREUR => 'En erreur sur Plat\'AU',
    ];

    public const INCONNU = 'unknown';
    public const EN_ATTENTE = 'awaiting';
    public const PRISE_EN_COMPTE = 'taken_into_account';
    public const A_RENVOYER = 'to_export';
    public const EN_ERREUR = 'in_error';

    public function getLabel(string $enumValue): ?string
    {
        return self::LABELS[$enumValue];
    }
}
