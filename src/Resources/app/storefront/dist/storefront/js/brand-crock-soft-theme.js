(window.webpackJsonp=window.webpackJsonp||[]).push([["brand-crock-soft-theme"],{"yq/h":function(n,o){window.onscroll=function(){window.pageYOffset>=s?e.classList.add("sticky"):e.classList.remove("sticky")};var e=document.getElementById("bcnavmenu"),s=e.offsetTop}},[["yq/h","runtime"]]]);
window.onload = function() {	
    function fadeOut(el) { // ** FADE OUT FUNCTION **
        el.style.opacity = 1;
        (function fade() {
            if ((el.style.opacity -= .0921) < 0) {
                el.style.display = "none";
            } else {
                requestAnimationFrame(fade);
            }
        })();
    };
    fadeOut(document.getElementById("preloader"));
	function funSetAttriLabel(clasnam,elemstr) {
		var sliderelem = document.getElementsByClassName(clasnam);
		for(var i=0; i<sliderelem.length; i++) {
			var j = i + 1;
			var aria_label = "tns"+j+elemstr;
			sliderelem[i].setAttribute("aria-label", aria_label);
		}
	}
	funSetAttriLabel("base-slider-controls-prev","_prev");
	funSetAttriLabel("base-slider-controls-next","_next");
	/* Alt attribute for CMS Images */
	function funSetAttriAlt(clasnam) {
		var cms_img_elem = document.getElementsByClassName(clasnam);
		for(var i=0; i<cms_img_elem.length;i++) {
			var src = document.getElementsByClassName(clasnam)[i].getAttribute("src");
			var spltd = src.split("/");
			var img_altname = spltd[spltd.length - 1].split(".")[0];
			document.getElementsByClassName(clasnam)[i].setAttribute("alt", img_altname);
		}
	}
	funSetAttriAlt("cms-image");
	funSetAttriAlt("cart-item-img");
	var addProducts = document.getElementById("addProductButton");
	var addPromotion = document.getElementById("addPromotion");
	if(addProducts != null)
		document.getElementById("addProductButton").setAttribute("aria-label", "addProductButton");
	if(addPromotion != null)
		document.getElementById("addPromotion").setAttribute("aria-label", "addPromotion");
}
var element = document.getElementsByTagName("body");
if(element[0].classList.contains('is-ctl-product')) {   /* Detail page */
	document.getElementsByClassName("js-magnifier-image")[0].addEventListener("click", productImageClick);  /* Product Image Click Event on Detail page */
	function productImageClick() {
		document.getElementById("bcnavmenu").style.display = "none";
		document.getElementsByClassName("modal-close")[0].addEventListener("click", productImageClose);
	}
	function productImageClose() {
		document.getElementById("bcnavmenu").style.display = "block";
	}
}
function bcSubMenuClick(mainMenuId,$submenuName){
	sessionStorage.setItem("mainMenuActive", mainMenuId);
	sessionStorage.setItem("mainMenuActiveTitle", submenuName);
}
function bcHomeClick() {
	sessionStorage.removeItem("mainMenuActive");
	sessionStorage.removeItem("mainMenuActiveTitle");
}
var currentMainMenuId = sessionStorage.getItem("mainMenuActive");
var mainMenuActiveTitle = sessionStorage.getItem("mainMenuActiveTitle");
if(currentMainMenuId) {
	document.querySelectorAll("[data-flyout-menu-trigger='"+currentMainMenuId+"']")[0].classList.add("bc-main-menu-active");
}
if(mainMenuActiveTitle) {
	document.querySelectorAll("[title='"+mainMenuActiveTitle+"']")[0].classList.add("bc-main-menu-active");
}