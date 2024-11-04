# Draw rules
## Foot
### National 1 à 3
1. Plus grand nombre de points au général
2. Plus grand nombre de points dans les confrontations directes.
3. Plus grande différence de buts dans les confrontations directes.
4. Plus grande différence de buts au général.
5. Plus grand nombre de buts marqués au général.
6. Meilleur place au fair-play (1 pt par carton jaune, 2 pts par carton rouges)
7. Tirage au sort

### ligue 1/2
1. Plus grand nombre de points au général
2. Plus grande différence de buts au général
3. Plus grand nombre de points dans les confrontations directes
4. Plus grande différence de buts dans les confrontations directes
5. Plus grand nombre de buts marqués dans les confrontations directes
6. Plus grand nombre de buts marqués à l'extérieur dans les confrontations directes
7. Plus grand nombre de buts marqués au général
8. Plus grand nombre de buts marqués à l'extérieur
9. Meilleure place au Fair-play (1 pt par carton jaune, 3 pts par carton rouges)

### Champions league
1. Plus grand nombre de points au général
2. Plus grand nombre de buts marqués au général
3. Plus grand nombre de buts marqués à l'extérieur au général
4. Plus grand nombre de victoires au général
5. Plus grand nombre de victoires à l'extérieur au général
6. Plus grand nombre de points obtenus collectivement par tous les adversaires rencontrés
7. Meilleure différence de buts collective de tous les adversaires rencontrés dans la phase de ligue
8. Plus grand nombre de buts marqués collectivement par tous les adversaires rencontrés dans la phase de ligue
9. Meilleure place au Fair-play (1 pt par carton jaune, 3 pt pour 2 cartons jaunes, 3 pts par carton rouges)

### J.O. foot masculin
1. Plus grand nombre de points au général
2. Plus grand nombre de buts marqués au général
3. Plus grand nombre de points dans les confrontations directes
4. Plus grande différence de buts dans les confrontations directes
5. Plus grand nombre de buts marqués dans les confrontations directes
6. Meilleure place au Fair-play (1 pt par carton jaune, 3 pt pour 2 cartons jaunes, 3 pts par carton rouges)

## Rugby
### Pro
1. Plus grand nombre de points au général.
2. Plus grand nombre de points dans les confrontations directes.
3. Plus grande différence de points (essais + transfo + pen) au général.
4. Plus grande différence de points (essais + transfo + pen) dans les confrontations directes.
5. Plus grande différence entre le nombre d'essais marqués et concédés dans les confrontations directes.
6. Plus grande différence entre le nombre d'essais marqués et concédés dans toutes les rencontres de la compétition ;
7. Plus grand nombre de points marqués (essais + transfo + pen) au général.
8. Plus grand nombre d'essais marqués dans toutes les rencontres de la compétition ;
9. Nombre de forfaits n'ayant pas entraîné de forfait général de la compétition ;
10. Place obtenue la saison précédente dans le Championnat de France.

### Amateur
1. Nombre de points « terrain » (voir art. 341) dans les confrontations directes.
2. Goal-average dans les confrontations directes.
3. Plus grande différence entre le nombre d'essais marqués et concédés dans les confrontations directes.
4. Goal-average sur l’ensemble des rencontres.
5. Plus grande différence entre le nombre d'essais marqués et concédés dans toutes les rencontres.
6. Plus grand nombre de points marqués (essais + transfo + pen) dans toutes les rencontres.
7. Plus grand nombre d'essais marqués dans toutes les rencontres.
8. Nombre de forfaits n'ayant pas entraîné de forfait général.
9. Nombre de semaines de suspension, liées aux sanctions disciplinaires, sur l’ensemble des
rencontres de la phase considérée (préliminaire, qualificative ou finale). Les suspensions
exprimées en weekends de compétition (liées, notamment, aux mesures sportives automatiques),
sont exclues de la comptabilisation des semaines de suspension précitées.
10. Classement à l'issue de la phase précédente.
11. Place obtenue la saison précédente dans la compétition nationale.

## Basket
### Pro
1. Plus grand nombre de points au général.
2. Plus grand nombre de points dans les confrontations directes.
3. Plus grand nombre de points (paniers) marqués  dans les confrontations directes.
4. Plus grande différence de points (paniers) au general.
5. Plus grand nombre de points marqués sur l'ensemble des rencontres.
6. Tirage au sort.

## Handball
### Pro
1. Plus grand nombre de points au général.
2. Plus grand nombre de points dans les confrontations directes.
3. Plus grande différence de buts dans les confrontations directes.
4. Plus grand nombre de buts marqués à l'extérieur dans les confrontations directes.
5. Plus grande différence de buts au général.
6. Plus grand nombre de buts marqués au général.
7. Le plus grand nombre de buts marqués à l'extérieur au général.
8. Tirage au sort.

# Code
from maxcoder
```
class a {
	public static $tests;

	public static function set_tests(array $tests) {
		self::$tests = $tests;
	}
	public static function goal_average($a, $b) {
		if ($a['goal_average'] == $b['goal_average']) {
			return 0;
		}
		return ($a['goal_average'] < $b['goal_average']) ? 1 : -1;
	}

	public static function goal_for($a, $b) {
		if ($a['goal_for'] == $b['goal_for']) {
			return 0;
		}
		return ($a['goal_for'] < $b['goal_for']) ? 1 : -1;
	}

	public static function make_range(&$items)
	{
		foreach (array_reverse(self::$tests) as $test) {
			uasort($items, self::class . '::' . $test);
		}
	}
}

// Tests par ordre de priorité, sortie de la DB du coup
$tests = ['goal_average', 'goal_for'];

a::set_tests($tests);
$items = [[
	'goal_average' => 5,
	'goal_for' => 5
],
[
	'goal_average' => 5,
	'goal_for' => 6
],
];
a::make_range($items);
Debug::dump($items);
```