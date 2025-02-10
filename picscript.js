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
                    console.log("request failed, retrying")
                    waitForNext(30)
                }
            }
        }
        request.send()
    }

    function updateSlide(newpage) {
        var temp = document.createElement('template')
        temp.innerHTML = newpage
        var newcontent = temp.content.querySelector('#content')
        var newscript = temp.content.querySelector('script')
        if(newcontent && newscript) {
            var oldcontent = container.querySelector('#content')
            oldcontent.replaceWith(newcontent)
            eval(newscript.innerHTML) // set new timeout
            waitForNext(timeout)
        } else {
            waitForNext(30)
        }
    }


    container.addEventListener('mousemove', mouseMove)

    // timeout is defined externally
    if(timeout > 0) {
        waitForNext(timeout)
    }

    document.getNext = getNext
}

document.addEventListener('DOMContentLoaded', init)
