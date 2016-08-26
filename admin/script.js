function make_sure(image) {
    return window.confirm("Är du säker på att du vill ta bort "+image+"?")
}

function dragstart(ev) {
    ev.dataTransfer.setData("draggedId", ev.target.id)
    ev.dataTransfer.setData("fromId", ev.target.parentNode.id)
}

function dragend(ev) {
    ev.dataTransfer.clearData()
}

function drop(ev) {
    ev.preventDefault()
    var droppedId = ev.dataTransfer.getData("draggedId")
    var copy = document.getElementById(droppedId).cloneNode(true)
    copy.id = ev.target.id + "_" + copy.id
    ev.target.appendChild(copy)
}

function remove_drop(event) {
    event.preventDefault()
    var item = event.dataTransfer.getData("draggedId")
    var origin = event.dataTransfer.getData("fromId")

    var form = document.getElementById(event.currentTarget.id)
    form.remove.value = item
    form.from.value = origin

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
    form.submit()
}
