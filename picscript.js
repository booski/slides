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
    
    waitForNext()
})

function waitForNext() {
    // timeout is defined externally
    if(timeout > 0) {
	window.setTimeout(function sleep() {
            getNext()
	}, timeout*1000)
    }
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
    
    request.onreadystatechange = function() {
	if (request.readyState == 4) {
	    if(request.status == 200) {
		updateSlide(request.responseText)
	    } else {
		waitForNext()
	    }
	}
    };
}
