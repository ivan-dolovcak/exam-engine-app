function updatePreferences(name, value)
{
    location.href
        = `/app/api/update_preferences.php?name=${name}&value=${value}`;
}

for (const configDropdown of document.getElementsByClassName("config-dropdown")) {
    configDropdown.value = preferences[configDropdown.id];
    configDropdown.addEventListener("change",
        () => updatePreferences(configDropdown.id, configDropdown.value));
}
