document.addEventListener('DOMContentLoaded', () => {
    // Highlight all same ids
    // Find all elements with a class that starts with 'id-'
    let elements = Array.from(document.querySelectorAll('[class*="id-"]'));

    // Add a 'mouseover' event listener to each element
    elements.forEach(element => {
        element.addEventListener('mouseover', () => {
            // Extract the number from the class
            let id = element.className.split(' ').find(c => c.startsWith('id-')).substring(3);

            // Find all elements with the same class and add a 'highlight' class to them
            let sameElements = document.querySelectorAll('.id-' + id);
            sameElements.forEach(e => e.classList.add('bracket-highlight'));
        });

        // Add a 'mouseout' event listener to each element to remove the 'highlight' class
        element.addEventListener('mouseout', () => {
            let id = element.className.split(' ').find(c => c.startsWith('id-')).substring(3);
            let sameElements = document.querySelectorAll('.id-' + id);
            sameElements.forEach(e => e.classList.remove('bracket-highlight'));
        });
    });

    // Highlight rank whith the prom/playoff/releg colors
    let rankingColorElements = document.querySelectorAll('.ranking-color');
    rankingColorElements.forEach(function(element) {
        let color = window.getComputedStyle(element, null).getPropertyValue('background-color');
    });

    // Bold fav team rank
    const favTeam = document.querySelectorAll('.fav-team');
    favTeam.forEach(element => {
        const children = element.querySelectorAll('*');

        children.forEach(child => {
            child.style.fontWeight = 'bold';
        });
    });


});

// Add current class in compet menu
var	current_url = window.location.href;
check_url = current_url.replace(window.location.origin, '');
jQuery('.roundmenu-title').each(function() {
    var link_href = jQuery(this).attr('href');
    if(link_href === check_url) {
        jQuery(this).addClass('current');
    }
});