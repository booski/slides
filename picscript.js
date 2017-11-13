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
	
	var timer = window.setTimeout(function hidecursor() {
	    if(!list.contains(selector)) {
		list.add(selector)
	    }
	}, 1500)
    })

    // timeout is defined externally
    if(timeout > 0) {
	waitForNext(timeout)
    }
})

function waitForNext(wait) {
    window.setTimeout(function sleep() {
        getNext()
    }, wait*1000)
}

function getNext() {
    var request = new XMLHttpRequest()
    request.open('GET', window.location.href, true)
    request.send()

    function updateSlide(newpage) {
	document.open()
	document.write(newpage)
	document.close()
    }
    
    function getSlide() {
	if(request.readyState == 4) {
	    if(request.status == 200) {
		updateSlide(request.responseText)
	    } else {
		// timeout is defined externally
		var wait = 30
		if(timeout > 0) {
		    wait = timeout
		}
		waitForNext(wait)
	    }
	}
    }
    
    request.onreadystatechange = getSlide
}
