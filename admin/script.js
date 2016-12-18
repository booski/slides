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

function select_file(event) {
    var fileinput = document.getElementById("uploadfile")
    fileinput.click()
}

function show_file(event) {
    var filefield = document.getElementById("filename")
    filefield.value = event.currentTarget.files[0].name
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
	return false
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
	return
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

function toggle_showsettings(event) {
    event.stopPropagation()
    toggle_settings(event)
}

function toggle_slidesettings(event) {
    event.stopPropagation()
    toggle_settings(event)

    var form = event.currentTarget.parentNode.querySelector('form')
    var input = form.endtime
    if(!input.date) {
	var date = new Date()
	input.id = date.getTime()
	var cal = new dhtmlXCalendarObject(input.id)
	cal.hideTime()
	cal.setSensitiveRange(date, null)
	input.date = cal
    }
}

function clear_date(event) {
    var form = event.currentTarget.parentNode.parentNode
    form.endtime.value = ''
}

function toggle_settings(event) {
    var form = event.currentTarget.parentNode.querySelector('form')
    var hidden = form.classList.contains('hidden')
    hide_forms()

    if(hidden) {
	show_form(form)
    }
}

function show_form(form) {
    if(form.classList.contains('hidden')) {
	form.classList.remove('hidden')
    }
}

function hide_forms() {
    var forms = document.querySelectorAll('form')
    for(var i=0; i < forms.length; i++) {
	var form = forms[i]
	if(form.classList.contains('hideable') && !form.classList.contains('hidden')) {
	    form.reset()
	    form.classList.add('hidden')
	}
    }
}

function halt(event) {
    event.stopPropagation()
}

function hide_error(event) {
    var errordiv = event.currentTarget
    errordiv.style.display = "none"
}
