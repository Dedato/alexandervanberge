/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can 
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function($) {

// Use this variable to set up the common and page specific functions. If you 
// rename this variable, you will also need to rename the namespace below.
var Roots = {
  // All pages
  common: {
    init: function() {
      // Minimize Header
      var bannermax = $('.banner').height();
      var bannermin;
      var winheight = $(window).height();
      var docheight;
      // Get document height when fully loaded
      $(window).load(function() {
        docheight = $(document).height();
      });
      // Get minimized header height based upon presence of submenu
      if ( $('.banner .navbar-collapse').has('ul.sub-nav').length ) {
        $('.banner, .wrap').addClass('submenu');
        bannermin = 80;
      } else {
        bannermin = 55;
      }
      $(window).scroll(function() {
        // If difference between window & document height is greater then difference between normal & minimized header
        if( ($(window).scrollTop() > 0) && ( (docheight - $(window).height()) > (bannermax - bannermin)) ) {
          //console.log( '(document height: ' + docheight + ' - window height: ' + $(window).height() + ') > (bannermax: ' + bannermax + ' - bannermin: ' + bannermin + ')' );
          $('.banner, .wrap').addClass('minimized');
          $('.banner h1').fitText(1.8, {minFontSize:'12px', maxFontSize:'20px'});
        } else {
          $('.banner, .wrap').removeClass('minimized');
          $('.banner h1').fitText(1.8, {minFontSize:'20px', maxFontSize:'40px'});
        }
      });
      // FitText
      $('.banner h1').fitText(1.8, {minFontSize:'20px', maxFontSize:'40px'});
      $('.main h1').fitText(1.8, {minFontSize:'15px', maxFontSize:'21px'});
      // Sharrre
      $('#shareme').sharrre({
         share: {
            twitter:    true,
            facebook:   true,
            linkedin:   true,
            googlePlus: true,
            pinterest:  false
         },
         buttons: {
           pinterest: {media: $('#shareme').data('img'), description: $('#shareme').data('text') }
         },
         template: '<div class="box"><div class="left"><i class="icon-share"></i><span class="share">Share</span></div><div class="middle"><a href="#" class="facebook"><i class="icon-facebook"></i></a><a href="#" class="twitter"><i class="icon-twitter"></i></a><a href="#" class="linkedin"><i class="icon-linkedin"></i></a><a href="#" class="googleplus"><i class="icon-googleplus"></i></a></div><div class="right">{total}</div></div>',
         urlCurl: '/wp-content/themes/alexandervanberge/lib/sharrre.php',
         enableHover:     false,
         //enableCounter:   false,
         enableTracking:  true,
         render: function(api, options){
            $(api.element).on('click', '.twitter', function() {
               api.openPopup('twitter');
            });
            $(api.element).on('click', '.facebook', function() {
               api.openPopup('facebook');
            });
            $(api.element).on('click', '.linkedin', function() {
               api.openPopup('linkedin');
            });
            $(api.element).on('click', '.googleplus', function() {
               api.openPopup('googlePlus');
            });
            $(api.element).on('click', '.pinterest', function() {
               api.openPopup('pinterest');
            });
         }
      });
      // FitVids
      $('.main .entry-video').fitVids();
    }
  },
  // Home page
  home: {
    init: function() {
      // Flexslider
      $('#slider').flexslider({
        animation:        "fade",
        animationLoop:    true,
        smoothHeight:     false,
        slideshowSpeed:   3000,
        animationSpeed:   600,
        controlNav:       false,
        directionNav:     false,
        slideshow:        true, // autoplay
        start: function(slider) {
          /* lazy load second image with picturefill */
          $(slider).find('.slides').children().eq(1).find('div.img span.lazy').each(function (i, elm) {
            var $elm    = $(elm);
            var content = $elm.html();
            var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
            var pic     = $pic[0];
            $elm.after($pic);  // insert the picture
            $elm.remove();     // remove the span
            picturefill();
          });
        },
        before: function(slider) {
          /* lazy load next image with picturefill */
          $(slider).find('.slides').children().eq(slider.animatingTo + 1).find('div.img span.lazy').each(function (i, elm) {
            var $elm    = $(elm);
            var content = $elm.html();
            var $class  = $elm.attr('class');
            var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
            var pic     = $pic[0];
            $elm.after($pic);  // insert the picture
            $elm.remove();     // remove the span
            picturefill();
          });
        }
      });
      // FitText
      $('h1').fitText(1.5, {minFontSize:'20px', maxFontSize:'40px'});
    }
  },
  // Portfolio
  post_type_archive_portfolio: {
    init: function() {
      // Flexslider
      $('#slider').imagesLoaded(function() {
        var thumbnavPos;
        $('#slider').flexslider({
          animation:          "fade",
          animationLoop:      false,
          smoothHeight:       false,
          slideshowSpeed:     4000,
          animationSpeed:     600,
          directionNav:       true,
          slideshow:          false, // autoplay
          controlNav:         true,
          controlsContainer:  '.newest .hentry #slider',
          manualControls:     '#thumb-nav .slide',
          start: function (slider) {
            thumbnavPos = $('#thumb-nav').position();
            /* lazy load second image with picturefill */
            $(slider).find('.slides').children().eq(1).find('span.lazy').each(function (i, elm) {
              var $elm    = $(elm);
              var content = $elm.html();
              var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
              var pic     = $pic[0];
              $elm.after($pic);  // insert the picture
              $elm.remove();     // remove the span
              picturefill();
            });
          },
          before: function (slider) {
            /* lazy load next image with picturefill */
            $(slider).find('.slides').children().eq(slider.animatingTo + 1).find('span.lazy').each(function (i, elm) {
              var $elm    = $(elm);
              var content = $elm.html();
              var $class  = $elm.attr('class');
              var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
              var pic     = $pic[0];
              $elm.after($pic);  // insert the picture
              $elm.remove();     // remove the span
              picturefill();
            });
          },
          after: function (slider) {
            /* lazy load clicked image with picturefill */
            $(slider).find('.slides').children().eq(slider.currentSlide).find('span.lazy').each(function (i, elm) {
              var $elm    = $(elm);
              var content = $elm.html();
              var $class  = $elm.attr('class');
              var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
              var pic     = $pic[0];
              $elm.after($pic);  // insert the picture
              $elm.remove();     // remove the span
              picturefill();
            });
            // ScrollTo Top
            var top;
            if ($('.banner').hasClass('minimized')) { top = 1;} else { top = 0; }
            $('html, body').animate({
              scrollTop: top
            }, 500, 'easeInOutQuad');
          }
        });
        // ScrollTo Thumb Nav
        $('.main .scrollto').click(function(e) {
          e.preventDefault();
          console.log(thumbnavPos.top);
          $('html, body').animate({
            scrollTop: thumbnavPos.top
          }, 500, 'easeInOutQuad');
        });
      });
      // View film effect
      if (Modernizr.touch) {
        // show the close overlay button
        $('.slides li .entry-video .effect .close-overlay').removeClass('hidden');
        // handle the adding of hover class when clicked
        $('.slides li .entry-video .effect').click(function(e){
          if (!$(this).hasClass('hover')) {
            $(this).addClass('hover');
          }
        });
        // handle the closing of the overlay
        $('.slides li .entry-video .effect .close-overlay').click(function(e){
          e.preventDefault();
          e.stopPropagation();
          if ($(this).closest('.effect').hasClass('hover')) {
            $(this).closest('.effect').removeClass('hover');
          }
        });
      } else {
        // handle the mouseenter functionality
        $('.slides li .entry-video .effect').mouseenter(function(){
          $(this).addClass('hover');
        })
        // handle the mouseleave functionality
        .mouseleave(function(){
          $(this).removeClass('hover');
        });
      }
    }
  },
  // Houses
  post_type_archive_library: {
    init: function() {
      // Flexslider
      $('#slider').imagesLoaded(function() {
        $('#thumbs').flexslider({
          animation:         "slide",
          controlNav:        false,
          animationLoop:     false,
          slideshow:         false,
          itemWidth:         134,
          itemMargin:        10,
          asNavFor:          '#slider'
        });
        $('#slider').flexslider({
          animation:        "fade",
          animationLoop:    false,
          smoothHeight:     false,
          slideshowSpeed:   4000,
          animationSpeed:   600,
          keyboard:         true,
          controlNav:       false,
          directionNav:     true,
          slideshow:        false, // autoplay
          sync:             "#thumbs",
          start: function (slider) {
            /* lazy load second image with picturefill */
            $(slider).find('.slides').children().eq(1).find('span.lazy').each(function (i, elm) {
              var $elm    = $(elm);
              var content = $elm.html();
              var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
              var pic     = $pic[0];
              $elm.after($pic);  // insert the picture
              $elm.remove();     // remove the span
              picturefill();
            });
          },
          before: function (slider) {
            /* lazy load next image with picturefill */
            $(slider).find('.slides').children().eq(slider.animatingTo + 1).find('span.lazy').each(function (i, elm) {
              var $elm    = $(elm);
              var content = $elm.html();
              var $class  = $elm.attr('class');
              var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
              var pic     = $pic[0];
              $elm.after($pic);  // insert the picture
              $elm.remove();     // remove the span
              picturefill();
            });
          },
          after: function (slider) {
            /* lazy load clicked image with picturefill */
            $(slider).find('.slides').children().eq(slider.currentSlide).find('span.lazy').each(function (i, elm) {
              var $elm    = $(elm);
              var content = $elm.html();
              var $class  = $elm.attr('class');
              var $pic    = $('<picture id="' + $elm.attr('id') + '">' + content + '</picture>');
              var pic     = $pic[0];
              $elm.after($pic);  // insert the picture
              $elm.remove();     // remove the span
              picturefill();
            });
            // ScrollTo Top
            var top;
            if ($('.banner').hasClass('minimized')) { top = 1;} else { top = 0; }
            $('html, body').animate({
              scrollTop: top
            }, 500, 'easeInOutQuad');
          }
        });
      });
      // Toggle form
      $('.newest footer .more-info').click(function(){
        $('.newest .form').toggle('slow');
        $(this).html(function(i, html){
          return html === '<i class="icon-envelope"></i>Less info' ? '<i class="icon-envelope"></i>More info' : '<i class="icon-envelope"></i>Less info';
        });
      });
    }
  },
  // Publications
  post_type_archive_publication: {
    init: function() {
      // View publication effect
      if (Modernizr.touch) {
        // show the close overlay button
        $('.close-overlay').removeClass('hidden');
        // handle the adding of hover class when clicked
        $('.effect').click(function(e){
          if (!$(this).hasClass('hover')) {
            $(this).addClass('hover');
          }
        });
        // handle the closing of the overlay
        $('.close-overlay').click(function(e){
          e.preventDefault();
          e.stopPropagation();
          if ($(this).closest('.effect').hasClass('hover')) {
            $(this).closest('.effect').removeClass('hover');
          }
        });
      } else {
        // handle the mouseenter functionality
        $('.effect').mouseenter(function(){
          $(this).addClass('hover');
        })
        // handle the mouseleave functionality
        .mouseleave(function(){
          $(this).removeClass('hover');
        });
      }
    }
  },
  // Infinite Scroll
  infscroll: {
    init: function() {
      // InfiniteScroll
      $('.grid').infinitescroll({
         loading: {
           msgText:        "Loading new items",
           finishedMsg:    "No more items to load",
           img:            "/wp-content/themes/alexandervanberge/assets/img/loading-icon.gif"
         },
         nextSelector:     ".post-nav li.next a",
         navSelector:      ".post-nav",
         itemSelector:     ".grid .hentry",
      },
      // Isotope callback
      function(newElements) {
        // Move #infscr-loading to parent in order to keep grid intact
        $('.grid').find('#infscr-loading').insertAfter($('.grid'));
        picturefill();
      });
    }
  },
  // Contact
  contact: {
    init: function() {
      // FitMaps
      $(".main .page").fitMaps({w:'100%', h:'350px'});
    }
  }
};

Roots.tax_portfolio_category = {
  init: Roots.post_type_archive_portfolio.init
};
Roots.tax_library_category = {
  init: Roots.post_type_archive_library.init
};
Roots.single_library = {
  init: Roots.post_type_archive_library.init
};


// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Roots;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
