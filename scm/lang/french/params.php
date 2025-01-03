<?php
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

####################################################
#                       French                     #
####################################################

// Params
$lang['scm.params.management']       = 'Paramètres de la compétition';
$lang['scm.params.tournament']       = 'Configuration du tournoi';
$lang['scm.params.practice']         = 'Configuration libre';
$lang['scm.params.bracket']          = 'Phases finales';
$lang['scm.params.ranking']          = 'Classement';
$lang['scm.params.options']          = 'Autres options';
$lang['scm.params.penalties']        = 'Pénalités';
$lang['scm.params.status']           = 'Statut';
$lang['scm.params.status.play']      = 'En compétition';
$lang['scm.params.status.forfeit']   = 'Forfait général';
$lang['scm.params.status.exempt']    = 'Exempt';

$lang['scm.games.number']         = 'Nombre de matchs';
$lang['scm.groups.number']        = 'Nombre de groupes';
$lang['scm.teams.per.group']      = 'Nombre maximum d\'équipes par groupe';
$lang['scm.hat.ranking']          = 'Classement au chapeau';
$lang['scm.hat.ranking.clue']     = '<span aria-label="Chaque équipe rencontre x équipes de chaque groupe"><i class="far fa-circle-question"></i></span>';
$lang['scm.hat.days']             = 'Nombre de matchs par équipe';
$lang['scm.hat.days.clue']        = 'En phase de groupes';
$lang['scm.fill.games']           = 'Remplir les matchs';
$lang['scm.fill.games.clue']      = '<span aria-label="Tous les matchs de groupe sont définis automatiquement avec les équipes de chaque groupe."><i class="far fa-circle-question"></i></span>';
$lang['scm.looser.brackets']      = 'Tableaux consolantes';
$lang['scm.looser.brackets.clue'] = '<span aria-label="Créer tous les matchs nécessaires pour attribuer un classement final pour toutes les équipes."><i class="far fa-circle-question"></i></span>';
$lang['scm.brackets.number']      = 'Nombre de tableaux consolantes';
$lang['scm.define.playgrounds']   = 'Définir les terrains';
$lang['scm.define.playgrounds.clue']   = '<span aria-label="Ajoute un champ dans la liste des match.<br />Utile dans le cas où plusieurs matchs se déroulent le même jour dans un même club."><i class="far fa-circle-question"></i></span>';
$lang['scm.victory.points']       = 'Points pour une victoire';
$lang['scm.draw.points']          = 'Points pour un match nul';
$lang['scm.loss.points']          = 'Points pour une défaite';
$lang['scm.promotion']            = 'Nombre d\'équipes promues';
$lang['scm.playoff.prom']         = 'Nombre d\'équipes en barrage de promotion';
$lang['scm.playoff.releg']        = 'Nombre d\'équipes en barrage de relégation';
$lang['scm.relegation']           = 'Nombre d\'équipes reléguées';
$lang['scm.fairplay.yellow']      = 'Points de fairplay par carton jaune';
$lang['scm.fairplay.red']         = 'Points de fairplay par carton rouge';

$lang['scm.finals.type'] = 'Type de match';
$lang['scm.finals.round'] = 'À élimination directe';
$lang['scm.finals.ranking'] = 'Par classement';
$lang['scm.rounds.number'] = 'Nombre de tours';
$lang['scm.rounds.number.clue'] = '
    <span aria-label="Par exemple, <strong>3</strong> donnera <strong>1/4 de finale, 1/2 finale, finale</strong>.
    <br /> Un tour de barrage est automatiquement ajouté si <strong>Classement au chapeau</strong> est coché."><i class="far fa-circle-question" aria-hidden="true"></i></span>
';
$lang['scm.draw.games']        = 'Matchs par tirage au sort';
$lang['scm.golden.goal']       = 'But en or';
$lang['scm.silver.goal']       = 'But en argent';
$lang['scm.has.overtime']      = 'Matchs avec prolongations';
$lang['scm.overtime.duration'] = 'Durée des prolongations';
$lang['scm.third.place']       = 'Match pour la troisième place';

$lang['scm.minutes.clue']       = 'En minutes';
$lang['scm.game.duration']      = 'Temps de jeu d\'un match';
$lang['scm.game.duration.clue'] = '
    <span aria-label="En minutes.<br />Prendre en compte la durée de la mi-temps."><i class="far fa-circle-question" aria-hidden="true"></i></span>';
$lang['scm.favorite.team']      = 'Équipe favorite';
$lang['scm.favorite.clue']      = '
    <span aria-label="Pour suivre une équipe dans les menus.
    <br />Met en surbrillance l\'équipe dans les résultats et classements."><i class="far fa-circle-question" aria-hidden="true"></i></span>';
$lang['scm.bonus.param']        = 'Points bonus';
$lang['scm.bonus.double']       = 'Of/défensif';
$lang['scm.bonus.single']       = 'Simple';

$lang['scm.ranking'] = 'Classement';
$lang['scm.ranking.type'] = 'Calcul du classement';
$lang['scm.ranking.type.clue'] = '
    <span class="d-block small">général = résultats issus des confrontations entre toutes les équipes.</span>
    <span class="d-block small">particulier = résultats issus des confrontations directes entre 2 équipes.</span>
';
$lang['scm.ranking.criterion']                = 'Critère ';
$lang['scm.ranking.general.points']           = 'Points - général';
$lang['scm.ranking.particular.points']        = 'Points - particulier';
$lang['scm.ranking.general.goal.average']     = 'Différence de buts - général';
$lang['scm.ranking.particular.goal.average']  = 'Différence de buts - particulier';
$lang['scm.ranking.general.goals.for']        = 'Buts marqués - général';
$lang['scm.ranking.particular.goals.for']     = 'Buts marqués - particulier';
$lang['scm.ranking.away.goals.for']           = 'Buts marqués - à l\'extérieur';
$lang['scm.ranking.general.goals.against']    = 'Buts encaissés - général';
$lang['scm.ranking.particular.goals.against'] = 'Buts encaissés - particulier';
$lang['scm.ranking.general.tries.average']    = 'Différence d\'essais marqués/encaissés - général';
$lang['scm.ranking.particular.tries.average'] = 'Différence d\'essais marqués/encaissés - particulier';
$lang['scm.ranking.win']                      = 'Victoires';
$lang['scm.ranking.win.away']                 = 'Victoires à l\'extérieur';
$lang['scm.ranking.general.fairplay']         = 'Fairplay - général';
$lang['scm.ranking.particular.fairplay']      = 'Fairplay - particulier';
$lang['scm.ranking.particular.tries.average'] = 'Différence d\'essais marqués/encaissés - particulier';

$lang['scm.warning.params.update'] = 'Les paramètres de la compétition <strong>:event_name</strong> ont été mis à jour.';
$lang['scm.warning.params.has.games'] = '
    Attention! Les matchs de cette compétition ont été définis.
    <br />Toute modification des paramètres en couleur nécessitera une réinitialisation de la liste des matchs.
';
?>
