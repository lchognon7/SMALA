//scroll auto

let mainDiv = document.querySelector('body');

$(mainDiv).scroll(function() {
    sessionStorage.scrollTop = $(this).scrollTop();
});
  
$(document).ready(function() {
if (sessionStorage.scrollTop != "undefined") {
    $(mainDiv).scrollTop(sessionStorage.scrollTop);
}
});



// SEARCHBARINSTANT
// init
"use strict";
let searchBarEl = document.querySelector("#search_input"); // la barre de recherche

// function
let searchQuerySelector = function (event) {
  let needle = event.target.value; // la valeur à chercher
  if (needle) {
    // éléments contenant un bout de cette classe
    for (const el of document.querySelectorAll(`#user_container_main_admin > div[class*="${needle}"]`)) {
      el.style.display = "grid";
    }
    // éléments ne contenant pas un bout de cette classe
    for (const el of document.querySelectorAll(`#user_container_main_admin > div:not([class*="${needle}"])`)) {
      el.style.display = "none";
    }
  } else {
    // if the search bar is empty
    for (const el of document.querySelectorAll("#user_container_main_admin > div")) {
      el.style.display = "none";
    }
  }
}

// events
searchBarEl.addEventListener("input", searchQuerySelector);




// init
"use strict";
let searchBarElImg = document.querySelector("#img_search_input"); // la barre de recherche

// function
let searchQuerySelectorImg = function (event) {
  let needle = event.target.value; // la valeur à chercher
  if (needle) {
    // éléments contenant un bout de cette classe
    for (const el of document.querySelectorAll(`#img_container_main_admin > div[class*="${needle}"]`)) {
      el.style.display = "grid";
    }
    // éléments ne contenant pas un bout de cette classe
    for (const el of document.querySelectorAll(`#img_container_main_admin > div:not([class*="${needle}"])`)) {
      el.style.display = "none";
    }
  } else {
    // if the search bar is empty
    for (const el of document.querySelectorAll("#img_container_main_admin > div")) {
      el.style.display = "none";
    }
  }
}

// events
searchBarElImg.addEventListener("input", searchQuerySelectorImg);