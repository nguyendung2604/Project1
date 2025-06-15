document.addEventListener("DOMContentLoaded", function () {
    const slider = document.getElementById("brand-slider");
    const itemWidth = slider.offsetWidth / 4;
    let autoScroll;

    function startAutoScroll() {
      autoScroll = setInterval(() => {
        if (slider.scrollLeft + itemWidth >= slider.scrollWidth - slider.clientWidth) {
          slider.scrollTo({ left: 0, behavior: "smooth" });
        } else {
          slider.scrollBy({ left: itemWidth, behavior: "smooth" });
        }
      }, 3000); // 3 giây chuyển một lần
    }

    startAutoScroll();
  });