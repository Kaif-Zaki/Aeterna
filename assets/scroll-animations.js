/**
 * Modern Scroll Animations using Intersection Observer API
 * Performs smooth fade-in, slide-up, scale animations on scroll
 */
(function () {
  "use strict";

  // Configuration
  const ANIMATION_CONFIG = {
    root: null,
    rootMargin: "0px 0px -50px 0px", // Trigger when 50px from bottom of viewport
    threshold: 0,
  };

  // Store animated elements to avoid re-animating
  const animatedElements = new WeakSet();

  /**
   * Apply animation to element based on data-scroll-animate attribute
   */
  function animateElement(element) {
    if (animatedElements.has(element)) return;

    const animationType =
      element.getAttribute("data-scroll-animate") || "fade-up";
    const delay = element.getAttribute("data-animation-delay") || "0ms";

    // Set custom properties for CSS animations
    element.style.setProperty("--animation-delay", delay);

    // Add animation class
    element.classList.add("scroll-animated", `animate-${animationType}`);
    animatedElements.add(element);
  }

  /**
   * Initialize intersection observer
   */
  function initIntersectionObserver() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateElement(entry.target);
          // Unobserve after animation to save performance
          observer.unobserve(entry.target);
        }
      });
    }, ANIMATION_CONFIG);

    // Observe all elements with scroll animation attributes
    document.querySelectorAll("[data-scroll-animate]").forEach((element) => {
      observer.observe(element);
    });
  }

  /**
   * Apply animations to page sections automatically
   */
  function autoAnimateSections() {
    const sections = document.querySelectorAll("section");
    const containers = document.querySelectorAll(
      ".container-luxury, .container",
    );
    const cards = document.querySelectorAll(
      "button[data-open-modal], button[data-gallery-image], a[href].group, [data-card-category] > div, .image-zoom",
    );
    const headings = document.querySelectorAll("h2, h3, h4, h5, h6");

    // Animate sections with staggered effect
    sections.forEach((section, index) => {
      if (!section.hasAttribute("data-scroll-animate")) {
        section.setAttribute("data-scroll-animate", "fade-up");
        section.setAttribute("data-animation-delay", `${index * 50}ms`);
      }
    });

    // Animate cards and interactive elements
    cards.forEach((card, index) => {
      if (!card.hasAttribute("data-scroll-animate")) {
        card.setAttribute("data-scroll-animate", "scale-fade");
        card.setAttribute("data-animation-delay", `${(index % 6) * 100}ms`);
      }
    });

    // Animate headings
    headings.forEach((heading) => {
      if (!heading.hasAttribute("data-scroll-animate")) {
        heading.setAttribute("data-scroll-animate", "fade-up");
      }
    });
  }

  /**
   * Initialize all scroll animations
   */
  function init() {
    // Auto animate common elements
    autoAnimateSections();

    // Setup intersection observer
    initIntersectionObserver();

    // Re-initialize if DOM changes (for dynamically loaded content)
    const observer = new MutationObserver(() => {
      const newElements = document.querySelectorAll(
        "[data-scroll-animate]:not(.scroll-animated)",
      );
      if (newElements.length > 0) {
        newElements.forEach((element) => {
          const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                animateElement(entry.target);
                sectionObserver.unobserve(entry.target);
              }
            });
          }, ANIMATION_CONFIG);
          sectionObserver.observe(element);
        });
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  // Start when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
