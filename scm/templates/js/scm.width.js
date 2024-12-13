
/**
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2024 06 12
 * @since       PHPBoost 6.0 - 2024 06 12
*/

document.addEventListener('DOMContentLoaded', () => {

    // Manage width
    const sm_widths = document.querySelectorAll('[class*="sm-width-"]');
    const md_widths = document.querySelectorAll('[class*="md-width-"]');
    const lg_widths = document.querySelectorAll('[class*="lg-width-"]');

    sm_widths.forEach((width) => {
        const classList = width.classList;
        const classes = width.className.split(' ');
        // Static
        if (window.innerWidth < 769) {
            classes.forEach(sizeClass => {
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
                classes.forEach(sizeClass => {
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
        if (window.innerWidth >= 769) {
            classes.forEach(sizeClass => {
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
            if (window.innerWidth >= 769) {
                classes.forEach(sizeClass => {
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
            classes.forEach(sizeClass => {
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
                classes.forEach(sizeClass => {
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

    // Manage columns
    // const sm_cols = document.querySelectorAll('[class*="sm-col-"]');
    // const md_cols = document.querySelectorAll('[class*="md-col-"]');
    // const lg_cols = document.querySelectorAll('[class*="lg-col-"]');

    // sm_cols.forEach((col) => {
    //     const classList = col.classList;
    //     const classes = col.className.split(' ');
    //     // Static
    //     if (window.innerWidth < 769) {
    //         classes.forEach(colClass => {
    //             children = colClass.querySelectorAll(':scope > *');
    //             if (colClass.startsWith('md-col-')) classList.remove(colClass);
    //             if (colClass.startsWith('lg-col-')) classList.remove(colClass);
    //             if (colClass.startsWith('sm-col-')) {
    //                 const size = colClass.split('-');
    //                 children.style.width = 'calc(100% / ' + size[2] + ' - var(--cell-gap) - 1px)';
    //             }
    //         });
    //     }
    //     // Dynamic
    //     const resizeHandler = () => {
    //         if (window.innerWidth < 769) {
    //             classes.forEach(colClass => {
    //                 children = colClass.querySelectorAll(':scope > *');
    //                 if (colClass.startsWith('md-col-')) classList.remove(colClass);
    //                 if (colClass.startsWith('lg-col-')) classList.remove(colClass);
    //                 if (colClass.startsWith('sm-col-')) {
    //                     const size = colClass.split('-');
    //                     children.style.width = 'calc(100% / ' + size[2] + ' - var(--cell-gap) - 1px)';
    //                 }
    //             });
    //         }
    //     }
    //     window.addEventListener('resize', resizeHandler);
    // });
    // md_cols.forEach((col) => {
    //     const classList = col.classList;
    //     const classes = col.className.split(' ');
    //     // Static
    //     if (window.innerWidth >= 769) {
    //         classes.forEach(colClass => {
    //             children = colClass.querySelectorAll(':scope > *');
    //             if (colClass.startsWith('sm-col-')) classList.remove(colClass);
    //             if (colClass.startsWith('lg-col-')) classList.remove(colClass);
    //             if (colClass.startsWith('md-col-')) {
    //                 const size = colClass.split('-');
    //                 children.style.width = 'calc(100% / ' + size[2] + ' - var(--cell-gap) - 1px)';
    //             }
    //         });
    //     }
    //     // Dynamic
    //     const resizeHandler = () => {
    //         if (window.innerWidth >= 769) {
    //             classes.forEach(colClass => {
    //                 children = colClass.querySelectorAll(':scope > *');
    //                 if (colClass.startsWith('sm-col-')) classList.remove(colClass);
    //                 if (colClass.startsWith('lg-col-')) classList.remove(colClass);
    //                 if (colClass.startsWith('md-col-')) {
    //                     const size = colClass.split('-');
    //                     children.style.width = 'calc(100% / ' + size[2] + ' - var(--cell-gap) - 1px)';
    //                 }
    //             });
    //         }
    //     }
    //     window.addEventListener('resize', resizeHandler);
    // });
    // lg_cols.forEach((col) => {
    //     const classList = col.classList;
    //     const classes = col.className.split(' ');
    //     // Static
    //     if (window.innerWidth >= 1366) {
    //         classes.forEach(colClass => {
    //             children = colClass.querySelectorAll(':scope > *');
    //             if (colClass.startsWith('sm-col-')) classList.remove(colClass);
    //             if (colClass.startsWith('md-col-')) classList.remove(colClass);
    //             if (colClass.startsWith('lg-col-')) {
    //                 const size = colClass.split('-');
    //                 children.style.width = 'calc(100% / ' + size[2] + ' - var(--cell-gap) - 1px)';
    //             }
    //         });
    //     }
    //     // Dynamic
    //     const resizeHandler = () => {
    //         if (window.innerWidth >= 1366) {
    //             classes.forEach(colClass => {
    //                 children = colClass.querySelectorAll(':scope > *');
    //                 if (colClass.startsWith('sm-col-')) classList.remove(colClass);
    //                 if (colClass.startsWith('md-col-')) classList.remove(colClass);
    //                 if (colClass.startsWith('lg-col-')) {
    //                     const size = colClass.split('-');
    //                     col.style.width = 'calc(100% / ' + size[2] + ' - var(--cell-gap) - 1px)';
    //                 }
    //             });
    //         }
    //     }
    //     window.addEventListener('resize', resizeHandler);
    // });
});