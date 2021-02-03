'use strict';

/***********************************************************
 * --------------------- BURGER MENU ----------------------
 ***********************************************************/
let burgerButton = document.querySelector('#burger');
let crossButton  = document.querySelector('#cross');
let navbarMobile = document.querySelector('#navbar-mobile');
let main         = document.querySelector('main');

burgerButton.addEventListener('click', displayNavbar);
crossButton.addEventListener('click', hideNavbar);
main.addEventListener('click', hideNavbar);

// On affiche la navbar et on remplace le burger par la croix
function displayNavbar() {
    burgerButton.classList.add('hidden');
    crossButton.classList.remove('hidden');
    navbarMobile.classList.remove('hidden');
}

// On enlève la nav et on remplace la croix par le burger
function hideNavbar() {
    burgerButton.classList.remove('hidden');
    crossButton.classList.add('hidden');
    navbarMobile.classList.add('hidden');
}

/***********************************************************
 * ----------------------- CAROUSEL -----------------------
 ***********************************************************/
document.addEventListener('DOMContentLoaded',()=>{
    
    // Récupération des boutons prev et next
    let prev = document.querySelector('.carousel-control-prev');
    let next = document.querySelector('.carousel-control-next');
    
    // Récupération des variables php
    let carouselImg1  = document.querySelector('#carousel-img-1').value;
    let carouselImg2  = document.querySelector('#carousel-img-2').value;
    let carouselImg3  = document.querySelector('#carousel-img-3').value;

    // Récupération des 'div' dans lesquelles on va ranger les images
    let carouselItem1 = document.querySelector('#carousel-item-1');
    let carouselItem2 = document.querySelector('#carousel-item-2');
    let carouselItem3 = document.querySelector('#carousel-item-3');

    // On insère les images dans le css
    let image1 = carouselItem1.style.backgroundImage = "url('"+carouselImg1+"')";
    let image2 = carouselItem2.style.backgroundImage = "url('"+carouselImg2+"')";
    let image3 = carouselItem3.style.backgroundImage = "url('"+carouselImg3+"')";

    // On range toutes les div dans un tableau
    const carouselImages = [carouselItem1, carouselItem2, carouselItem3];

    let i = 0; // On initialise le compteur à 0
    let indexImg = carouselImages.length - 1;
    let currentImg = carouselImages[i];

    // On affiche l'image courante
    currentImg.classList.add('slideactive');

    /** Quand on click sur précédent **/
    prev.addEventListener('click', prevImg); // event desktop
    prev.addEventListener('touchmove', prevImg); // event mobile

    /** Quand on click sur suivant **/
    next.addEventListener('click', nextImg);
    next.addEventListener('touchmove', nextImg); // event mobile

    function prevImg(){
        i--; // on décrémente le compteur, puis on réalise la même chose que pour la fonction "suivante"

        if( i >= 0 ){
            // On cache les images
            for(let i = 0 ; i <= indexImg ; i++) {
                currentImg.classList.remove('slideactive');
            }
            currentImg = carouselImages[i];
            // On affiche l'image courante
            currentImg.classList.add('slideactive');
        }
        else{
            i = 2;

            // On cache les images
            for(let i = 0 ; i <= indexImg ; i++) {
                currentImg.classList.remove('slideactive');
            }
            currentImg = carouselImages[i];
            // On affiche l'image courante
            currentImg.classList.add('slideactive');
        }
    }
    
    function nextImg(){
        i++; // on incrémente le compteur

        if( i <= indexImg ){
            // On cache les images
            for(let i = 0 ; i <= indexImg ; i++) {
                currentImg.classList.remove('slideactive');
            }
            currentImg = carouselImages[i];
            // On affiche l'image courante
            currentImg.classList.add('slideactive');
        }
        else{
            i = 0;

            // On cache les images
            for(let i = 0 ; i <= indexImg ; i++) {
                currentImg.classList.remove('slideactive');
            }
            currentImg = carouselImages[i];
            // On affiche l'image courante
            currentImg.classList.add('slideactive');
        }
    }

    function slideImg(){
        setTimeout(function(){ // on utilise une fonction anonyme
        
            nextImg();
        
            slideImg(); // on oublie pas de relancer la fonction à la fin
    
        }, 7000); // on définit l'intervalle à 7000 millisecondes (7s)
    }
    
    slideImg(); // enfin, on lance la fonction une première fois

});