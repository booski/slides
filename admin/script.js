function make_sure(image) {
    if(window.confirm("Är du säker på att du vill ta bort "+image+"?")) {
	document.getElementById(image).click()
    }
}
