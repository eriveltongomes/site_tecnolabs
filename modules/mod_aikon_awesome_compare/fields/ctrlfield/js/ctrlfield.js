// define the global namespace for our field show hide map
// *this is populated later, and is ready for use on dom ready
if (typeof window.ctrlFieldGlobal == 'undefined'){window.ctrlFieldGlobal = {};}



// handling object
function CtrlFieldManager (sourceMap, inputOptions) {
    var bla,
        map = sourceMap,
        that = this;

    //default options
    var options = {
                      animationSpeed    : 150,
                      triggeringEvent   : 'click',
                      JVersion          : 3
                   };



    /**
     * Overwrites obj1's values with obj2's and adds obj2's if non existent in obj1.
     * @param obj1      object
     * @param obj2      object
     * @returns obj3 a new object based on obj1 and obj2
     */
    var combineObj = function (obj1, obj2, overwrite){
        var obj3 = {};
        for (var attrname in obj1) {
            obj3[attrname] = obj1[attrname];
        }
        for (var attrname in obj2) {
            obj3[attrname] = obj2[attrname];
        }
        return obj3;
    };


    var getTargetPerJVersion = function (selector){
        var $target = '';

        if (options.JVersion > 2) {
            $target = jQuery(selector).parents('.control-group');
        } else {
            /* TODO: add j2.5 selector here */
            $target = jQuery(selector).parents('li');
        }

        return $target;
    };

    // hides a field by selector
    var hideField = function (selector, instant){
        var $target = getTargetPerJVersion(selector);

        //if instant use display none, else animate
        if( typeof instant != 'undefined' && instant == true) {
            $target.stop().css('display', 'none');
        } else {
            $target.stop().fadeOut(options.animationSpeed);
        }

    };

    // shows a field by selector
    var showField = function(selector, instant) {

        var $target = getTargetPerJVersion(selector);
        //if instant use display none, else animate
        if( typeof instant != 'undefined' && instant == true) {
            $target.stop().css('display', 'block');
        } else {
            $target.stop().fadeIn( { 
								duration	: options.animationSpeed,
								done		: function () { $target.css('opacity', '1');} 
								} );
        }
    };

    /*
     * binds show hide fields actions to the an element. defiend by a selector and a control object
     * @param string selector
     * @param object control - has an 'enable' and 'disable' array. (also must be very agreeable,  probable, and doable (get it? :D ))
     */
    var bindCtrl = function (selector, control){
        var  labelSelector = '[for="' + selector.substring(1) + '"]';

        jQuery(selector).css('border','3px solid blue');
        jQuery(selector + ', ' + labelSelector).on(options.triggeringEvent, function (e) {
            // hide and show relevant elemetns
            try {
                jQuery.each(control.enable, function(index, value) {
                    showField(value)
                });

                jQuery.each(control.disable, function(index, value) {
                    hideField(value);
                });

            } catch(err) {
                console.log('could not bind control. maybe it has no arrays?');
            }
            return true;
        });
    };

    /*
     * for each field in map, reads it's status, and shows/ hides other fields
     */
    this.activateByDom = function (){
        var tmpSpeed = options.animationSpeed;
        // itterate over the map. if control found to be active, trigger the triggering even to simulate a click
        jQuery.each(map, function (index, controls) {
            // for each actual button on display
            jQuery.each(controls, function (index, control) {
                var $elm = jQuery(index);
                if ($elm.prop('checked') == true){
                    // temprarily change speed to 1 so that the fields will be displayed / shown instantly, then restore it
                    options.animationSpeed = 1;
                    $elm.trigger('click');
                    options.animationSpeed = tmpSpeed;

                }
            });
        });
    };

   /*
    * load options into the object, overrides default
    */
    this.loadOptions = function (inputOptions){
        if (typeof inputOptions == 'object') {
           options =  combineObj(options, inputOptions);
        }
    };

    this.init = function () {
        // handle options
        that.loadOptions(inputOptions);

        // bind events
        // for each controlling field
        jQuery.each(map, function (index, controls) {
            // for each actual button on display
            jQuery.each(controls, function (index, control) {
                bindCtrl(index, control); // index is the ID of the control including '#', control is object of two arrays
            });
        });
        //activate by dom
        manager.activateByDom();
    };

    return this;
}



jQuery(document).ready(function () {
    var fieldGlobal = window.ctrlFieldGlobal,
        options     =   {
            animationSpeed : 350,
            triggeringEvent : 'click',
            JVersion        : window.ctrlFieldJVersion
        };

    manager = new CtrlFieldManager(fieldGlobal, options );
    manager.init();
});

jQuery(document).load(function () {
    // make sure something else did not make a mess and the hidden field remaind hidden
    manager.activateByDom();
});

