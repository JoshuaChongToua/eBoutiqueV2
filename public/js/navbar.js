import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.esm.browser.min.js';
import splitting from "https://cdn.skypack.dev/splitting@1.0.6";

const heroEl = document.querySelector(".hero");
const fullSizeWrapEl = heroEl.querySelector(".hero__fullsize");
const contentEls = heroEl.querySelectorAll(".swiper .content");
const contentFullsizeEls = Array.from(contentEls, (el) => {
    const clone = el.cloneNode(true);
    splitting({ target: clone, by: "words" });
    clone.classList.add(
        "hero__content",
        "hero__content--hidden",
        "content--hero"
    );
    clone.classList.remove("content--slide");
    return clone;
});

contentFullsizeEls.forEach((el) => fullSizeWrapEl.appendChild(el));

const state = {
    topContent: null,
    bottomContent: null
};

function slideChange(swiper) {
    const total = swiper.slides.length - swiper.loopedSlides * 2;
    const contentIndex = (swiper.activeIndex - swiper.loopedSlides) % total;

    const content = contentFullsizeEls[contentIndex];
    if (!content) return;

    if (state.bottomContent) {
        state.bottomContent.classList.remove("hero__content--bottom");
        state.bottomContent.classList.add("hero__content--hidden");
    }

    if (state.topContent) {
        state.topContent.classList.remove("hero__content--top");
        state.topContent.classList.add("hero__content--bottom");
    }

    state.bottomContent = state.topContent;
    state.topContent = content;

    const slidetRect = swiper.slides[swiper.activeIndex].getBoundingClientRect();
    const parentRect = heroEl.getBoundingClientRect();

    content.style.transition = "none";
    content.style.left = slidetRect.left - parentRect.left + "px";
    content.style.top = slidetRect.top - parentRect.top + "px";
    content.style.width = slidetRect.width + "px";
    content.style.height = slidetRect.height + "px";
    content.style.borderRadius = "var(--border-radius, 0)";

    content.getBoundingClientRect();

    content.classList.remove("hero__content--hidden");
    content.classList.add("hero__content--top", "hero__content--grow");

    content.style.transition = "";
    content.style.left = "";
    content.style.top = "";
    content.style.width = "";
    content.style.height = "";
    content.style.borderRadius = "";

    const onShowTextEnd = (event) => {
        if (event.target !== event.currentTarget) {
            event.currentTarget.classList.remove("hero__content--show-text");
            event.currentTarget.removeEventListener("transitionend", onShowTextEnd);
        }
    };

    const onGrowEnd = (event) => {
        event.currentTarget.classList.remove("hero__content--grow");
        event.currentTarget.classList.add("hero__content--show-text");
        event.currentTarget.addEventListener("transitionend", onShowTextEnd);
    };

    content.addEventListener("transitionend", onGrowEnd, { once: true });
}

function swiperInit(swiper) {
    const total = swiper.slides.length - swiper.loopedSlides * 2;
    const contentIndex = (swiper.activeIndex - swiper.loopedSlides) % total;

    const content = contentFullsizeEls[contentIndex];
    if (!content) return;

    content.classList.remove("hero__content--hidden");
    content.classList.add("hero__content--top");
    state.topContent = content;
}


const swiper = new Swiper(".swiper", {
    slidesPerView: 4.5,
    spaceBetween: 35,
    loop: true,
    speed: 1000,
    simulateTouch: false,

    autoplay: {
        delay: 5000
    },

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev"
    },
    on: { realIndexChange: slideChange, init: swiperInit }
});

