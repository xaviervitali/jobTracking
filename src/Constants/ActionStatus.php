<?php 
class ActionStatus
{
    // Actions finales
    public const REJET_CANDIDATURE = 1;
    public const ACCEPTATION = 1;
    public const REFUS_OFFRE = 1;

    // Actions non finales
    public const ENTRETIEN_TELEPHONIQUE = 0;
    public const ENTRETIEN_VISIO = 0;
    public const ENTRETIEN_PHYSIQUE = 0;
    public const ENTRETIEN_TECHNIQUE = 0;
    public const TESTS_PSYCHOMETRIQUES = 0;
    public const EXERCICES_PRACTIQUES = 0;
    public const RELANCE_TELEPHONIQUE = 0;
    public const RELANCE_MAIL = 0;
    public const RELANCE_PRESENTIEL = 0;
    public const ABANDON_CANDIDATURE = 0;

    // Méthode pour récupérer les actions avec leur statut
    public static function getActions(): array
    {
        return [
            'Rejet de candidature' => self::REJET_CANDIDATURE,
            'Acceptation' => self::ACCEPTATION,
            'Refus de l\'offre' => self::REFUS_OFFRE,
            'Entretien téléphonique' => self::ENTRETIEN_TELEPHONIQUE,
            'Entretien visio' => self::ENTRETIEN_VISIO,
            'Entretien physique' => self::ENTRETIEN_PHYSIQUE,
            'Entretien technique' => self::ENTRETIEN_TECHNIQUE,
            'Tests psychométriques' => self::TESTS_PSYCHOMETRIQUES,
            'Exercices pratiques ou études de cas' => self::EXERCICES_PRACTIQUES,
            'Relance téléphonique' => self::RELANCE_TELEPHONIQUE,
            'Relance mail' => self::RELANCE_MAIL,
            'Relance en présentiel' => self::RELANCE_PRESENTIEL,
            'Abandon de la candidature' => self::ABANDON_CANDIDATURE,
        ];
    }

    // Méthode pour récupérer le statut final/non final
    public static function isFinalAction(string $action): bool
    {
        return in_array($action, [
            self::REJET_CANDIDATURE,
            self::ACCEPTATION,
            self::REFUS_OFFRE,
            self::ABANDON_CANDIDATURE,
        ]);
    }
}
