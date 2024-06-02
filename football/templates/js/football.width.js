
// @copyright   &copy; 2005-2023 PHPBoost
// @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
// @author      Sebastien LARTIGUE <babsolune@phpboost.com>
// @version     PHPBoost 6.0 - last update: 2023 10 07
// @since       PHPBoost 6.0 - 2023 10 07

document.addEventListener('DOMContentLoaded', () => {

    // Manage table width and bgcolor
    const widths = document.querySelectorAll('[class*="width-"]');
    widths.forEach((width) => {
        const classes = width.className.split(' ');

        classes.forEach((sizeClass) => {
            if (sizeClass.startsWith('width-')) {
                const size = sizeClass.split('-');
                if (size[1] == "pc") size[2] = '%';
                width.style.width = size[2] + size[1];
            }
        });
    });
});