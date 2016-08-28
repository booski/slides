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

function remove_drop(event) {
    event.preventDefault()
    var item = event.dataTransfer.getData("draggedId")
    var origin = event.dataTransfer.getData("fromId")

    var form = document.getElementById(event.currentTarget.id)
    form.remove.value = item
    form.from.value = origin

    save_scroll()
    form.submit()
}

function add_drop(event) {
    event.preventDefault()
    var item = event.dataTransfer.getData("draggedId")
    var origin = event.dataTransfer.getData("fromId")

    if(origin != "slides") {
	return false;
    }

    var form = document.getElementById(event.currentTarget.id)
    form.add.value = item
    save_scroll()
    form.submit()
}
