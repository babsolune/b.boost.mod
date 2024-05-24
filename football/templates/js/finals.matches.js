// Find all elements with a class that starts with 'id-'
let elements = Array.from(document.querySelectorAll('[class*="id-"]'));

// Add a 'mouseover' event listener to each element
elements.forEach(element => {
    element.addEventListener('mouseover', () => {
        // Extract the number from the class
        let id = element.className.split(' ').find(c => c.startsWith('id-')).substring(3);

        // Find all elements with the same class and add a 'highlight' class to them
        let sameElements = document.querySelectorAll('.id-' + id);
        sameElements.forEach(e => e.parentElement.classList.add('finals-highlight'));
    });

    // Add a 'mouseout' event listener to each element to remove the 'highlight' class
    element.addEventListener('mouseout', () => {
        let id = element.className.split(' ').find(c => c.startsWith('id-')).substring(3);
        let sameElements = document.querySelectorAll('.id-' + id);
        sameElements.forEach(e => e.parentElement.classList.remove('finals-highlight'));
    });
});