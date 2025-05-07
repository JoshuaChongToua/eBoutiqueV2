document.addEventListener("turbo:load", function () {

    document.querySelectorAll(".back-content").forEach(function (element) {
        let imagesData = element.getAttribute("data-images");

        if (!imagesData) return;

        let images = JSON.parse(imagesData);
        if (!Array.isArray(images) || images.length === 0) return;

        let index = 0;

        // Création de 2 couches pour éviter le jump
        let imgLayer1 = document.createElement("div");
        let imgLayer2 = document.createElement("div");

        imgLayer1.classList.add("image-layer");
        imgLayer2.classList.add("image-layer");

        element.appendChild(imgLayer1);
        element.appendChild(imgLayer2);

        imgLayer1.style.backgroundImage = `url('${images[index]}')`;
        imgLayer1.style.opacity = "1";

        function prechargerImage(src, callback) {
            let img = new Image();
            img.src = src;
            img.onload = callback;
        }

        function changeBackground() {
            let nextIndex = (index + 1) % images.length;
            let nextImage = images[nextIndex];

            prechargerImage(nextImage, function () {
                let activeLayer = imgLayer1.style.opacity === "1" ? imgLayer2 : imgLayer1;
                let inactiveLayer = imgLayer1.style.opacity === "1" ? imgLayer1 : imgLayer2;

                activeLayer.style.backgroundImage = `url('${nextImage}')`;
                activeLayer.style.opacity = "1";
                inactiveLayer.style.opacity = "0";

                index = nextIndex;
            });
        }

        setInterval(changeBackground, 3000);
    });
});