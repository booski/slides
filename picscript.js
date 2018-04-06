function init() {
    var container = document.querySelector('.container')
    var list = container.classList
    var selector = 'nocursor'
    var timer = null

    function hideCursor() {
	if(!list.contains(selector)) {
	    list.add(selector)
	}
    }
    
    function mouseMove(event) {
	if(timer) {
	    window.clearTimeout(timer)
	}
	
	if(list.contains(selector)) {
	    list.remove(selector)
	}
	
	timer = window.setTimeout(hideCursor, 1500)
    }

    function waitForNext(wait) {
	if(wait) {
	    window.setTimeout(getNext, wait*1000)
	}
    }

    function getNext() {
	var request = new XMLHttpRequest()
	var showid = window.location.href.split('?')[1].split('=')[1]
	request.open('GET', 'get.php?id=' + showid, true)
	request.onreadystatechange = function getSlide() {
	    if(request.readyState == 4) {
		if(request.status == 200) {
		    updateSlide(request.responseText)
		} else {
		    var wait = 30
		    waitForNext(wait)
		}
	    }
	}
	request.send()
    }

    function updateSlide(newpage) {
	container.removeChild(container.querySelector('#content'))
	container.removeChild(container.querySelector('script'))
	container.innerHTML = newpage
	eval(container.querySelector('script').innerHTML)
	waitForNext(timeout)
    }


    container.addEventListener('mousemove', mouseMove)

    // timeout is defined externally
    if(timeout > 0) {
	waitForNext(timeout)
    }
    
    document.getNext = getNext
}

document.addEventListener('DOMContentLoaded', init)
