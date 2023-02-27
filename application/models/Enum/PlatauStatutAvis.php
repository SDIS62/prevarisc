<?php

class Model_Enum_PlatauStatutAvis
{
    public const LABELS = [
        self::INCONNU => null,
        self::EN_COURS => 'En cours',
        self::TRAITE => 'Traité',
        self::A_RENVOYER => 'Traité (en attente de renvoi)',
    ];

    public const INCONNU = 'unknown';
    public const EN_COURS = 'in_progress';
    public const TRAITE = 'treated';
    public const A_RENVOYER = 'to_export';

    public function getLabel(string $enumValue): string
    {
        return self::LABELS[$enumValue];
    }
}
