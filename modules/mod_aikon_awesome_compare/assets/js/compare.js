jQuery(document).ready(function(jQuery){
    //check if the .cd-image-container is in the viewport
    //if yes, animate it
    checkPosition(jQuery('.cd-image-container'));
    jQuery(window).on('scroll', function(){
        checkPosition(jQuery('.cd-image-container'));
    });

    //make the .cd-handle element draggable and modify .cd-resize-img width according to its position
    jQuery('.cd-image-container').each(function(){
        var actual = jQuery(this);
        drags(actual.find('.cd-handle'), actual.find('.cd-resize-img'), actual, actual.find('.cd-image-label[data-type="original"]'), actual.find('.cd-image-label[data-type="modified"]'));
    });

    //upadate images label visibility
    jQuery(window).on('resize', function(){
        jQuery('.cd-image-container').each(function(){
            var actual = jQuery(this);
            updateLabel(actual.find('.cd-image-label[data-type="modified"]'), actual.find('.cd-resize-img'), 'left');
            updateLabel(actual.find('.cd-image-label[data-type="original"]'), actual.find('.cd-resize-img'), 'right');
        });
    });
});

function checkPosition(container) {
    container.each(function(){
        var actualContainer = jQuery(this);
        if( jQuery(window).scrollTop() + jQuery(window).height()*0.5 > actualContainer.offset().top) {
            actualContainer.addClass('is-visible');
        }
    });
}

//draggable funtionality - credits to http://css-tricks.com/snippets/jquery/draggable-without-jquery-ui/
function drags(dragElement, resizeElement, container, labelContainer, labelResizeElement) {
    dragElement.on("mousedown vmousedown", function(e) {
        dragElement.addClass('draggable');
        resizeElement.addClass('resizable');

        var dragWidth = dragElement.outerWidth(),
            xPosition = dragElement.offset().left + dragWidth - e.pageX,
            containerOffset = container.offset().left,
            containerWidth = container.outerWidth(),
            halfHandle = Math.round(jQuery('.cd-handle').width() / 2) + 1,
            minLeft = containerOffset -halfHandle,
            maxLeft = containerOffset + containerWidth - dragWidth + halfHandle,
            throttle = 0,
            isFireFox = navigator.userAgent.toLowerCase().indexOf('firefox'),
            isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor),
            isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
            isWebkit = isChrome || isSafari;


        dragElement.parents().on("mousemove vmousemove", function(e) {
            if (isFireFox){  // firefox throttle
                if (throttle < 6){
                    throttle++;
                    return;
                }
                throttle = 0;

            } else if (isWebkit){  // webkit throttle
                if (throttle != true){
                    setTimeout(function(){throttle = true;},3);
                    return;
                }
                clearTimeout(throttle);
                throttle = false;
            } else { // ie. and others
                if (throttle < 4){
                    throttle++;
                    return;
                }
                throttle = 0;
            }

            leftValue = e.pageX + xPosition - dragWidth;

            //constrain the draggable element to move inside his container
            if(leftValue < minLeft ) {
                leftValue = minLeft;
            } else if ( leftValue > maxLeft) {
                leftValue = maxLeft;
            }

            widthValue = (leftValue + dragWidth/2 - containerOffset)*100/containerWidth+'%';

            jQuery('.draggable').css('left', widthValue);

            jQuery('.resizable').css('width', widthValue);

           // updateLabel(labelResizeElement, resizeElement, 'left');
            //updateLabel(labelContainer, resizeElement, 'right');
            
        }).on("mouseup vmouseup", function(e){
            dragElement.removeClass('draggable');
            resizeElement.removeClass('resizable');
            dragElement.parents().off("mousemove vmousemove");
        });
        e.preventDefault();
    }).on("mouseup vmouseup", function(e) {
        dragElement.removeClass('draggable');
        resizeElement.removeClass('resizable');
        dragElement.parents().off("mousemove vmousemove");
    });
}

function updateLabel(label, resizeElement, position) {
    return true;
}