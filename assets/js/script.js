// Simple counter animation
const counters = document.querySelectorAll('.counter');
const speed = 200; // Lower = faster

counters.forEach(counter => {
    const updateCount = () => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText.replace('+', '').replace(/,/g, '');
        const increment = Math.max(Math.floor(target / speed), 1);

        if (count < target) {
            counter.innerText = new Intl.NumberFormat().format(count + increment) + '+';
            setTimeout(updateCount, 10);
        } else {
            counter.innerText = new Intl.NumberFormat().format(target) + '+';
        }
    };

    updateCount();
});