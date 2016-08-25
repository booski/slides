function make_sure(image) {
    return window.confirm("Är du säker på att du vill ta bort "+image+"?")
}

function dragstart(ev) {
    ev.dataTransfer.setData("draggedId", ev.target.id)
    //ev.effectAllowed = "copyMove"
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
