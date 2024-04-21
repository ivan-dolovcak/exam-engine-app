function openOverlay(overlayName)
{
    document.getElementById("overlay-" + overlayName).style.display = "block";
}

function closeOverlay(overlayName)
{
    document.getElementById("overlay-" + overlayName).style.display = "none";
}

for (const btnCloseOverlay of document.getElementsByClassName("btn-close-overlay")) {
    btnCloseOverlay.addEventListener("click",
        () => closeOverlay(btnCloseOverlay.dataset.overlay))
}

for (const btnOpenOverlay of document.getElementsByClassName("btn-open-overlay")) {
    btnOpenOverlay.addEventListener("click",
        () => openOverlay(btnOpenOverlay.dataset.overlay))
}
