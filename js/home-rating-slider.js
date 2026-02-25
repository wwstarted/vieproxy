/**
 * Home Rating Vertical Slider
 * Two columns: left scrolls down, right scrolls up
 * Infinite scroll with duplicated cards
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    const columns = document.querySelectorAll(".home-rating-column");

    if (!columns.length) return;

    columns.forEach(function (column) {
      const track = column.querySelector(".home-rating-column__track");
      if (!track) return;

      const cards = Array.from(track.children);

      if (cards.length === 0) return;

      // Clone all cards and append to create infinite loop effect
      cards.forEach(function (card) {
        const clone = card.cloneNode(true);
        track.appendChild(clone);
      });

      // Calculate total height for seamless loop
      const firstCard = cards[0];
      const cardHeight = firstCard.offsetHeight;
      const gap = 20; // Match CSS gap
      const totalHeight = (cardHeight + gap) * cards.length;

      // Set animation duration based on number of cards
      const baseSpeed = 40; // seconds for full cycle
      const duration = baseSpeed * (cards.length / 6); // Adjust speed
      track.style.animationDuration = duration + "s";
    });
  });
})();
