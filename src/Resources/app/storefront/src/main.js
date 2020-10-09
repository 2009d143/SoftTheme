/*eslint no-undef: "error"*/
/*use jQuery*/

window.onscroll = function() {myFunction()};

var navbar = document.getElementById('bcnavmenu');
var sticky = navbar.offsetTop;
function myFunction() {
    if (window.pageYOffset >= sticky) {
        navbar.classList.add('sticky')
    } else {
        navbar.classList.remove('sticky');
    }
}
