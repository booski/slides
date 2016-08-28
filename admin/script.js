function upload(event) {
    //event.preventDefault()
    console.log("hek")
    var form = document.getElementById(event.currentTarget.parentNode.id)
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
