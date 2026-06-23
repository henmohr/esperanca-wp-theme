(function () {
  function initCarousel(root) {
    var track = root.querySelector('.site-banner-carousel__track');
    var slides = Array.prototype.slice.call(root.querySelectorAll('.site-banner-carousel__slide'));
    var prev = root.querySelector('[data-carousel-prev]');
    var next = root.querySelector('[data-carousel-next]');
    var dotsWrap = root.querySelector('[data-carousel-dots]');

    if (!track || slides.length === 0) {
      return;
    }

    var index = 0;
    var timer = null;
    var interval = parseInt(root.getAttribute('data-interval'), 10) || 6000;
    var autoplay = root.getAttribute('data-autoplay') !== 'false' && slides.length > 1;
    var dots = [];

    function applyState() {
      track.style.transform = 'translateX(' + (-index * 100) + '%)';

      slides.forEach(function (slide, slideIndex) {
        var active = slideIndex === index;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');

        if (active) {
          slide.removeAttribute('tabindex');
        } else {
          slide.setAttribute('tabindex', '-1');
        }
      });

      dots.forEach(function (dot, dotIndex) {
        dot.classList.toggle('is-active', dotIndex === index);
        dot.setAttribute('aria-pressed', dotIndex === index ? 'true' : 'false');
      });
    }

    function goTo(newIndex) {
      index = (newIndex + slides.length) % slides.length;
      applyState();
    }

    function stop() {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    }

    function start() {
      if (!autoplay || timer) {
        return;
      }

      timer = window.setInterval(function () {
        goTo(index + 1);
      }, interval);
    }

    if (dotsWrap && slides.length > 1) {
      slides.forEach(function (_, dotIndex) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'site-banner-carousel__dot';
        dot.setAttribute('aria-label', 'Ir para a imagem ' + (dotIndex + 1));
        dot.addEventListener('click', function () {
          goTo(dotIndex);
          stop();
          start();
        });
        dotsWrap.appendChild(dot);
        dots.push(dot);
      });
    }

    if (prev) {
      prev.addEventListener('click', function () {
        goTo(index - 1);
        stop();
        start();
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        goTo(index + 1);
        stop();
        start();
      });
    }

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);

    applyState();
    start();
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-banner-carousel').forEach(initCarousel);
  });
})();
