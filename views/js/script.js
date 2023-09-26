(function ($) {
  'use strict';

  // Preloader
  $(window).on('load', function () {
    $('#preloader').fadeOut('slow', function () {
      $(this).remove();
    });
  });



  // e-commerce touchspin
  $('input[name=\'product-quantity\']').TouchSpin();


  // Video Lightbox
  $(document).on('click', '[data-toggle="lightbox"]', function (event) {
    event.preventDefault();
    $(this).ekkoLightbox();
  });



  //Hero Slider
  $('.hero-slider').slick({
    // autoplay: true,
    infinite: true,
    arrows: true,
    prevArrow: '<button type=\'button\' class=\'heroSliderArrow prevArrow tf-ion-chevron-left\'></button>',
    nextArrow: '<button type=\'button\' class=\'heroSliderArrow nextArrow tf-ion-chevron-right\'></button>',
    dots: true,
    autoplaySpeed: 7000,
    pauseOnFocus: false,
    pauseOnHover: false
  });
  $('.hero-slider').slickAnimation();


})(jQuery);










// Store the current scroll position
let lastScrollTop = 0;

// Store the header element
const header = document.querySelector('.top-header');

// Store the height of the header
const headerHeight = header.offsetHeight;

// Function to handle scroll events
function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // Check if the user is scrolling down
    if (scrollTop > lastScrollTop) {
        // Scrolling down, hide the header
        header.style.transform = `translateY(-${headerHeight}px)`;
    } else {
        // Scrolling up, show the header
        header.style.transform = 'translateY(0)';
    }

    // Update the last scroll position
    lastScrollTop = scrollTop;
}

// Add a scroll event listener to the window
window.addEventListener('scroll', handleScroll);
