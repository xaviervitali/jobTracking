<?php

namespace App\Enums;

enum ActionStatus : string 
{
        // Actions finales
    case REJET_CANDIDATURE =  'Rejet de candidature';
    case ACCEPTATION = 'Acceptation';
    case REFUS_OFFRE = 'Refus de l\'offre';
    case ABANDON_CANDIDATURE = 'Abandon de la candidature';
    case ANNONCE_SUPPRIMEE = 'Annonce supprimée';

        // Actions non finales
    case ENTRETIEN_TELEPHONIQUE = 'Entretien téléphonique';
    case ENTRETIEN_VISIO = 'Entretien visio';
    case ENTRETIEN_PHYSIQUE =  'Entretien physique';
    case ENTRETIEN_TECHNIQUE = 'Entretien technique';
    case TESTS_PSYCHOMETRIQUES = 'Tests psychométriques';
    case EXERCICES_PRACTIQUES =  'Exercices pratiques ou études de cas';
    case RELANCE_TELEPHONIQUE = 'Relance téléphonique';
    case RELANCE_MAIL = 'Relance mail';
    case RELANCE_PRESENTIEL = 'Relance en présentiel';
    case ENVOI_CANDIDATURE = 'Envoi candidature';


    // Méthode pour récupérer les actions avec leur statut
    public static function getActions(): array {
        $actions = array_filter(self::cases(), function($action) {
           return $action->value !== self::getStartActionName();
        });

        return array_map(fn ($case) => $case->value, $actions);
    }

    public static function getActionsWithStartAction(): array {
        return array_map(fn ($case) => $case->value, self::cases());
    }
    // Méthode pour récupérer le statut final/non final
    public static function isFinalAction(string $action): bool
    {
        return in_array($action, [
            self::REJET_CANDIDATURE,
            self::ACCEPTATION,
            self::REFUS_OFFRE,
            self::ABANDON_CANDIDATURE,
            self::ANNONCE_SUPPRIMEE,
        ]);
    }

    public static function getStartActionName():string
    {
        return self::ENVOI_CANDIDATURE->value;
    }
}
