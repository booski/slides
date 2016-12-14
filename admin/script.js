function save_scroll() {
    var scroll = window.pageYOffset
    localStorage.setItem("scroll", scroll)
}

function restore_scroll() {
    var scroll = localStorage.getItem("scroll")
    if(scroll) {
	window.scroll(0, scroll)
	localStorage.setItem("scroll", "")
    }
}

function submit_form(event) {
    event.preventDefault()
    var form = event.currentTarget.parentNode
    save_scroll()
    form.submit()
}

function dragstart(event) {
    event.dataTransfer.setData("draggedId", event.target.id)
    event.dataTransfer.setData("fromId", event.target.parentNode.id)
}

function dragend(event) {
    event.dataTransfer.clearData()
}

function add_drop(event) {
    event.preventDefault()
    var item = event.dataTransfer.getData("draggedId")
    var origin = event.dataTransfer.getData("fromId")

    if(origin != "slides") {
	return false;
    }

    var form = event.currentTarget.children.add
    form.add.value = item
    save_scroll()
    form.submit()
}

function remove_drop(event) {
    event.preventDefault()
    var item = event.dataTransfer.getData("draggedId")
    var origin = event.dataTransfer.getData("fromId")

    if(!confirm_removal(item, origin)) {
	return;
    }

    var form = event.currentTarget
    form.remove.value = item
    form.from.value = origin

    save_scroll()
    form.submit()
}

function confirm_removal(itemid, originid) {

    if(originid == "shows") {
	return window.confirm("Är du säker på att du vill ta bort den här visningsytan (id: "+itemid+")?")
	
    } else if(originid == "slides") {
	return window.confirm("Är du säker på att du vill ta bort den här bilden?")
	
    } else {
	return true
    }
}

function toggle_settings(event) {
    var form = event.currentTarget.parentNode.querySelector('form')
    if(form.classList.contains('hidden')) {
	form.classList.remove('hidden')
    } else {
	revert_endform(form)
    }
}

function revert_size(event) {
    var form = event.currentTarget.parentNode
    form.width.value = form.start_width.value
    form.height.value = form.start_height.value
}

function revert_time(event) {
    var form = event.currentTarget.parentNode
    form.timeout.value = form.start_timeout.value
}

function revert_endtime(event) {
    var form = event.currentTarget.parentNode
    revert_endform(form)
}

function revert_endform(form) {
    form.endtime.value = form.start_endtime.value
    form.classList.add('hidden');
}

function hide_error(event) {
    var errordiv = event.currentTarget
    errordiv.style.display = "none"
}
