function openOverlay(overlayID)
{
    const overlay = document.getElementById(overlayID);
    overlay.style.display = "block";
    document.querySelector(`#${overlayID} > form > label`).focus();
}

function closeOverlay(overlayID)
{
    document.getElementById(overlayID).style.display = "none";
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
