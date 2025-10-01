document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar nav a');
    sidebarLinks.forEach(link => {
        // Remove any existing active class
        link.classList.remove('active');
        
        // If the href matches the current page, add active
        if (link.getAttribute('href') && path.endsWith(link.getAttribute('href'))) {
            link.classList.add('active');
        }
        
        // Special case: If on view_assessment.php or create_assessment.php, make assessments link active
        if ((path.includes('view_assessment.php') || path.includes('create_assessment.php')) && link.getAttribute('href') === 'assessments.php') {
            link.classList.add('active');
        }
    });
}); 