
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

document.addEventListener('DOMContentLoaded', () => {

    // Manage table width
    const sm_widths = document.querySelectorAll('[class*="sm-width-"]');
    const md_widths = document.querySelectorAll('[class*="md-width-"]');
    const lg_widths = document.querySelectorAll('[class*="lg-width-"]');

    sm_widths.forEach((width) => {
        const classList = width.classList;
        const classes = width.className.split(' ');
        // Static
        if (window.innerWidth < 769) {
            classes.forEach((sizeClass) => {
                if (sizeClass.startsWith('md-width-')) classList.remove(sizeClass);
                if (sizeClass.startsWith('lg-width-')) classList.remove(sizeClass);
                if (sizeClass.startsWith('sm-width-')) {
                    const size = sizeClass.split('-');
                    if (size[2] == "pc") size[2] = '%';
                    width.style.width = size[3] + size[2];
                }
            });
        }
        // Dynamic
        const resizeHandler = () => {
            if (window.innerWidth < 769) {
                classes.forEach((sizeClass) => {
                    if (sizeClass.startsWith('md-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('lg-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('sm-width-')) {
                        const size = sizeClass.split('-');
                        if (size[2] == "pc") size[2] = '%';
                        width.style.width = size[3] + size[2];
                    }
                });
            }
        }
        window.addEventListener('resize', resizeHandler);
    });
    md_widths.forEach((width) => {
        const classList = width.classList;
        const classes = width.className.split(' ');
        // Static
        if (window.innerWidth >= 769 && window.innerWidth < 1366) {
            classes.forEach((sizeClass) => {
                    if (sizeClass.startsWith('sm-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('lg-width-')) classList.remove(sizeClass);
                if (sizeClass.startsWith('md-width-')) {
                    const size = sizeClass.split('-');
                    if (size[2] == "pc") size[2] = '%';
                    width.style.width = size[3] + size[2];
                }
            });
        }
        // Dynamic
        const resizeHandler = () => {
            if (window.innerWidth >= 769 && window.innerWidth < 1366) {
                classes.forEach((sizeClass) => {
                    if (sizeClass.startsWith('sm-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('lg-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('md-width-')) {
                        const size = sizeClass.split('-');
                        if (size[2] == "pc") size[2] = '%';
                        width.style.width = size[3] + size[2];
                    }
                });
            }
        }
        window.addEventListener('resize', resizeHandler);
    });
    lg_widths.forEach((width) => {
        const classList = width.classList;
        const classes = width.className.split(' ');
        // Static
        if (window.innerWidth >= 1366) {
            classes.forEach((sizeClass) => {
                if (sizeClass.startsWith('sm-width-')) classList.remove(sizeClass);
                if (sizeClass.startsWith('md-width-')) classList.remove(sizeClass);
                if (sizeClass.startsWith('lg-width-')) {
                    const size = sizeClass.split('-');
                    if (size[2] == "pc") size[2] = '%';
                    width.style.width = size[3] + size[2];
                }
            });
        }
        // Dynamic
        const resizeHandler = () => {
            if (window.innerWidth >= 1366) {
                classes.forEach((sizeClass) => {
                    if (sizeClass.startsWith('sm-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('md-width-')) classList.remove(sizeClass);
                    if (sizeClass.startsWith('lg-width-')) {
                        const size = sizeClass.split('-');
                        if (size[2] == "pc") size[2] = '%';
                        width.style.width = size[3] + size[2];
                    }
                });
            }
        }
        window.addEventListener('resize', resizeHandler);
    });
});