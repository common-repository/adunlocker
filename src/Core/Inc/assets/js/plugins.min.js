/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/
!function(){"use strict";window.addEventListener("DOMContentLoaded",()=>{function s(s){let t=!0;for(let e of this.parentElement.querySelectorAll("span.dashicons"))t?(e.classList.remove("dashicons-star-empty"),e.classList.add("dashicons-star-filled"),t=s.target!==e):(e.classList.remove("dashicons-star-filled"),e.classList.add("dashicons-star-empty"))}function t(s){const t=Math.floor(Math.random()*Math.floor(2)),e=s.querySelectorAll("span.dashicons");for(let s=4;s>0&&s>4-t;s--)e[s].classList.remove("dashicons-star-filled"),e[s].classList.add("dashicons-star-empty")}for(let e of document.querySelectorAll(".pp-rating-stars"))t(e),e.addEventListener("mouseover",s)})}();