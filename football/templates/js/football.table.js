
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

document.addEventListener('DOMContentLoaded', () => {

    // Manage table width and bgcolor
    const widths = document.querySelectorAll('[class*="width-"]');
    widths.forEach((width) => {
        const classes = width.className.split(' ');

        classes.forEach((sizeClass) => {
            if (sizeClass.startsWith('width-')) {
                const size = sizeClass.split('-');
                width.style.width = size.pop() + '%';
            }
        });
    });

    let rankingColorElements = document.querySelectorAll('.ranking-color');
    rankingColorElements.forEach(function(element) {
        let color = window.getComputedStyle(element, null).getPropertyValue('background-color');
        console.log(color);
    });
});