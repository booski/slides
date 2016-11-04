document.addEventListener('DOMContentLoaded', function init() {
    var container = document.querySelector('.container')
    var list = container.classList
    var selector = 'nocursor'
    var timer = null
    
    container.addEventListener('mousemove', function mousemove(event) {
	if(timer) {
	    window.clearTimeout(timer)
	}
	
	if(list.contains(selector)) {
	    list.remove(selector)
	}
	
	timer = window.setTimeout(function hidecursor() {
	    if(!list.contains(selector)) {
		list.add(selector)
	    }
	}, 1500)
    })

    if(timeout > 0) {
	window.setTimeout(function sleep() {
            window.location.reload(true);
	}, timeout*1000);
    }
})
