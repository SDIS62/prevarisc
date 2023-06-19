<?php

class Model_Enum_PlatauStatutAvis
{
    public const LABELS = [
        self::INCONNU => null,
        self::EN_COURS => 'En attente de complétude sur Prevarisc',
        self::TRAITE => 'Traité sur Plat\'AU',
        self::A_RENVOYER => 'Traité sur Plat\'AU (en attente de renvoi)',
        self::EN_ERREUR => 'En erreur sur Plat\'AU',
    ];

    public const INCONNU = 'unknown';
    public const EN_COURS = 'in_progress';
    public const TRAITE = 'treated';
    public const A_RENVOYER = 'to_export';
    public const EN_ERREUR = 'in_error';

    public function getLabel(string $enumValue): ?string
    {
        return self::LABELS[$enumValue];
    }
}
