
/**
 * Scroll to the first not played game
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

document.addEventListener('DOMContentLoaded', () => {
    let games = Array.from(document.querySelectorAll('.cell-game')); // define all games
    const now = Date.now(); // define actual timestamp
    let not_played = []; // init games list
    games.forEach((game) => {
        const gamedate = game.dataset.date * 1000; // get timestamp of the game
        if (gamedate && gamedate > now) // compare game timestamp to now
            not_played.push(game); // add game into games list
    });

    const button = document.getElementById('next-game');
    if (not_played.length > 0)
    {
        button.addEventListener('click', () => { // by clicking on button
            const parent = not_played[0].parentNode; // find parent of first of games
            const target = parent.previousElementSibling; // find previous main date of games, the real target
            target.scrollIntoView({behavior: 'smooth'}); // scroll to target
        });
    }
    else
    {
        button.style.display = 'none';
    }
});