function make_sure(image) {
    return window.confirm("Är du säker på att du vill ta bort "+image+"?")
}

function dragstart_handler(ev) {
    ev.dataTransfer.setData("text", ev.target.id)
    ev.effectAllowed = "copyMove"
}

function dragend_handler(ev) {
    ev.dataTransfer.clearData()
}

function drop(ev) {
    ev.preventDefault()
    var droppedId = ev.dataTransfer.getData("text")
    var copy = document.getElementById(droppedId).cloneNode(true)
    copy.id = ev.target.id + "_" + copy.id
    ev.target.appendChild(copy)
}
