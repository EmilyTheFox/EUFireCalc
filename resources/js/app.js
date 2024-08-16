import './bootstrap';
import.meta.glob([
    '../images/**',
]);

document.addEventListener('DOMContentLoaded', () => {
    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
  
    // Add a click event on each of them
    $navbarBurgers.forEach( el => {
        el.addEventListener('click', () => {
    
            // Get the target from the "data-target" attribute
            const target = el.dataset.target;
            const $target = document.getElementById(target);
    
            // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
            el.classList.toggle('is-active');
            $target.classList.toggle('is-active');
    
        });
    });

    const lightIcon = document.getElementById("light-icon");
    const darkIcon = document.getElementById("dark-icon");

    // Check if dark mode is preferred
    const darkModeMediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    let darkMode = darkModeMediaQuery.matches;

    // Set dark-mode class on body if darkMode is true and pick icon
    if (darkMode) {
        lightIcon.setAttribute("display", "block");
        darkIcon.setAttribute("display", "none");
    } else {
        lightIcon.setAttribute("display", "none");
        darkIcon.setAttribute("display", "block");
    }
    
    // Toggle dark mode on button click
    function awoooooga() {
        // Toggle darkMode variable
        darkMode = !darkMode;

        // Toggle dark-mode class on body
        if (darkMode) {
            document.documentElement.setAttribute('data-theme', 'dark')
        } else {
            document.documentElement.setAttribute('data-theme', 'light')
        }

        // Toggle light and dark icons
        if (darkMode) {
            lightIcon.setAttribute("display", "block");
            darkIcon.setAttribute("display", "none");
        } else {
            lightIcon.setAttribute("display", "none");
            darkIcon.setAttribute("display", "block");
        }
    }
    
    lightIcon.addEventListener('click', () => {
        awoooooga();
    });
    darkIcon.addEventListener('click', () => {
        awoooooga();
    });

});


