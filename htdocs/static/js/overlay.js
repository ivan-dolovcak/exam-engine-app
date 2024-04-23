function openOverlay(overlayID)
{
    const overlay = document.getElementById(overlayID);
    overlay.classList.add("fade");
    const firstLabel = document.querySelector(`#${overlayID} > form > label`);
    if (firstLabel)
        firstLabel.focus();
}

function closeOverlay(overlayID)
{
    document.getElementById(overlayID).classList.remove("fade");
}

for (const btnCloseOverlay of document.getElementsByClassName("btn-close-overlay")) {
    const overlayID = `overlay-${btnCloseOverlay.dataset.overlay}`;

    btnCloseOverlay.addEventListener("click",
        () => closeOverlay(overlayID));
    document.getElementById(overlayID).addEventListener("keydown",
        (e) => { if (e.key === "Escape") closeOverlay(overlayID) });
}

for (const btnOpenOverlay of document.getElementsByClassName("btn-open-overlay")) {
    const overlayID = `overlay-${btnOpenOverlay.dataset.overlay}`;

    btnOpenOverlay.addEventListener("click",
        () => openOverlay(overlayID));
}
