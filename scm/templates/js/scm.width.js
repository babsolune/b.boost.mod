
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
        // Static
        if (window.innerWidth > 769) {
            classes.forEach((sizeClass) => {
                if (sizeClass.startsWith('width-')) {
                    const size = sizeClass.split('-');
                    if (size[1] == "pc") size[1] = '%';
                    width.style.width = size[2] + size[1];
                }
            });
        }
        // Dynamic
        const resizeHandler = () => {
            if (window.innerWidth > 769) {
                classes.forEach((sizeClass) => {
                    if (sizeClass.startsWith('width-')) {
                        const size = sizeClass.split('-');
                        if (size[1] == "pc") size[1] = '%';
                        width.style.width = size[2] + size[1];
                    }
                });
            } else {
                classes.forEach((sizeClass) => {
                    if (sizeClass.startsWith('width-')) {
                        width.style.width = 100 + '%';
                    }
                });
            }
        }
        window.addEventListener('resize', resizeHandler);
    });
});