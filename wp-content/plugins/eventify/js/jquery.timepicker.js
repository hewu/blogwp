/*
 * A time picker for jQuery
 * Based on original timePicker by Sam Collet (http://www.texotela.co.uk) -
 * copyright (c) 2006 Sam Collett (http://www.texotela.co.uk)
 *
 * Dual licensed under the MIT and GPL licenses.
 * Copyright (c) 2009 Anders Fajerson
 * @name     timePicker
 * @version  0.2
 * @author   Anders Fajerson (http://perifer.se)
 * @example  jQuery("#mytime").timePicker();
 * @example  jQuery("#mytime").timePicker({step:30, startTime:"15:00", endTime:"18:00"});
 */

(function(jQuery){
  jQuery.fn.timePicker = function(options) {
    // Build main options before element iteration
    var settings = jQuery.extend({}, jQuery.fn.timePicker.defaults, options);

    return this.each(function() {
      jQuery.timePicker(this, settings);
    });
  };

  jQuery.timePicker = function (elm, settings) {
    var e = jQuery(elm)[0];
    return e.timePicker || (e.timePicker = new jQuery._timePicker(e, settings));
  };

  jQuery._timePicker = function(elm, settings) {

    var tpOver = false;
    var keyDown = false;
    var startTime = timeToDate(settings.startTime, settings);
    var endTime = timeToDate(settings.endTime, settings);

    jQuery(elm).attr('autocomplete', 'OFF'); // Disable browser autocomplete

    var times = [];
    var time = new Date(startTime); // Create a new date object.
    while(time <= endTime) {
      times[times.length] = formatTime(time, settings);
      time = new Date(time.setMinutes(time.getMinutes() + settings.step));
    }

    var jQuerytpDiv = jQuery('<div class="time-picker'+ (settings.show24Hours ? '' : ' time-picker-12hours') +'" style="width:75px;"></div>');
    var jQuerytpList = jQuery('<ul style="width:75px;"></ul>');

    // Build the list.
    for(var i = 0; i < times.length; i++) {
      jQuerytpList.append("<li>" + times[i] + "</li>");
    }
    jQuerytpDiv.append(jQuerytpList);
    // Append the timPicker to the body and position it.
    var elmOffset = jQuery(elm).offset();
    jQuerytpDiv.appendTo('body').css({'top':elmOffset.top, 'left':elmOffset.left}).hide();

    // Store the mouse state, used by the blur event. Use mouseover instead of
    // mousedown since Opera fires blur before mousedown.
    jQuerytpDiv.mouseover(function() {
      tpOver = true;
    }).mouseout(function() {
      tpOver = false;
    });

    jQuery("li", jQuerytpList).mouseover(function() {
      if (!keyDown) {
        jQuery("li.selected", jQuerytpDiv).removeClass("selected");
        jQuery(this).addClass("selected");
      }
    }).mousedown(function() {
       tpOver = true;
    }).click(function() {
      setTimeVal(elm, this, jQuerytpDiv, settings);
      tpOver = false;
    });

    var showPicker = function() {
      if (jQuerytpDiv.is(":visible")) {
        return false;
      }
      jQuery("li", jQuerytpDiv).removeClass("selected");

      // Show picker. This has to be done before scrollTop is set since that
      // can't be done on hidden elements.
      jQuerytpDiv.show();

      // Try to find a time in the list that matches the entered time.
      var time = elm.value ? timeStringToDate(elm.value, settings) : startTime;
      var startMin = startTime.getHours() * 60 + startTime.getMinutes();
      var min = (time.getHours() * 60 + time.getMinutes()) - startMin;
      var steps = Math.round(min / settings.step);
      var roundTime = normaliseTime(new Date(0, 0, 0, 0, (steps * settings.step + startMin), 0));
      roundTime = (startTime < roundTime && roundTime <= endTime) ? roundTime : startTime;
      var jQuerymatchedTime = jQuery("li:contains(" + formatTime(roundTime, settings) + ")", jQuerytpDiv);

      if (jQuerymatchedTime.length) {
        jQuerymatchedTime.addClass("selected");
        // Scroll to matched time.
        jQuerytpDiv[0].scrollTop = jQuerymatchedTime[0].offsetTop;
      }
      return true;
    };
    // Attach to click as well as focus so timePicker can be shown again when
    // clicking on the input when it already has focus.
    jQuery(elm).focus(showPicker).click(showPicker);
    // Hide timepicker on blur
    jQuery(elm).blur(function() {
      if (!tpOver) {
        jQuerytpDiv.hide();
      }
    });
    // Keypress doesn't repeat on Safari for non-text keys.
    // Keydown doesn't repeat on Firefox and Opera on Mac.
    // Using kepress for Opera and Firefox and keydown for the rest seems to
    // work with up/down/enter/esc.
    var event = (jQuery.browser.opera || jQuery.browser.mozilla) ? 'keypress' : 'keydown';
    jQuery(elm)[event](function(e) {
      var jQueryselected;
      keyDown = true;
      var top = jQuerytpDiv[0].scrollTop;
      switch (e.keyCode) {
        case 38: // Up arrow.
          // Just show picker if it's hidden.
          if (showPicker()) {
            return false;
          };
          jQueryselected = jQuery("li.selected", jQuerytpList);
          var prev = jQueryselected.prev().addClass("selected")[0];
          if (prev) {
            jQueryselected.removeClass("selected");
            // Scroll item into view.
            if (prev.offsetTop < top) {
              jQuerytpDiv[0].scrollTop = top - prev.offsetHeight;
            }
          }
          else {
            // Loop to next item.
            jQueryselected.removeClass("selected");
            prev = jQuery("li:last", jQuerytpList).addClass("selected")[0];
            jQuerytpDiv[0].scrollTop = prev.offsetTop - prev.offsetHeight;
          }
          return false;
          break;
        case 40: // Down arrow, similar in behaviour to up arrow.
          if (showPicker()) {
            return false;
          };
          jQueryselected = jQuery("li.selected", jQuerytpList);
          var next = jQueryselected.next().addClass("selected")[0];
          if (next) {
            jQueryselected.removeClass("selected");
            if (next.offsetTop + next.offsetHeight > top + jQuerytpDiv[0].offsetHeight) {
              jQuerytpDiv[0].scrollTop = top + next.offsetHeight;
            }
          }
          else {
            jQueryselected.removeClass("selected");
            next = jQuery("li:first", jQuerytpList).addClass("selected")[0];
            jQuerytpDiv[0].scrollTop = 0;
          }
          return false;
          break;
        case 13: // Enter
          if (jQuerytpDiv.is(":visible")) {
            var sel = jQuery("li.selected", jQuerytpList)[0];
            setTimeVal(elm, sel, jQuerytpDiv, settings);
          }
          return false;
          break;
        case 27: // Esc
          jQuerytpDiv.hide();
          return false;
          break;
      }
      return true;
    });
    jQuery(elm).keyup(function(e) {
      keyDown = false;
    });
    // Helper function to get an inputs current time as Date object.
    // Returns a Date object.
    this.getTime = function() {
      return timeStringToDate(elm.value, settings);
    };
    // Helper function to set a time input.
    // Takes a Date object.
    this.setTime = function(time) {
      elm.value = formatTime(normaliseTime(time), settings);
      // Trigger element's change events.
      jQuery(elm).change();
    };

  }; // End fn;

  // Plugin defaults.
  jQuery.fn.timePicker.defaults = {
    step:30,
    startTime: new Date(0, 0, 0, 0, 0, 0),
    endTime: new Date(0, 0, 0, 23, 30, 0),
    separator: ':',
    show24Hours: true
  };

  // Private functions.

  function setTimeVal(elm, sel, jQuerytpDiv, settings) {
    // Update input field
    elm.value = jQuery(sel).text();
    // Trigger element's change events.
    jQuery(elm).change();
    // Keep focus for all but IE (which doesn't like it)
    if (!jQuery.browser.msie) {
      elm.focus();
    }
    // Hide picker
    jQuerytpDiv.hide();
  }

  function formatTime(time, settings) {
    var h = time.getHours();
    var hours = settings.show24Hours ? h : (((h + 11) % 12) + 1);
    var minutes = time.getMinutes();
    return formatNumber(hours) + settings.separator + formatNumber(minutes) + (settings.show24Hours ? '' : ((h < 12) ? ' AM' : ' PM'));
  }

  function formatNumber(value) {
    return (value < 10 ? '0' : '') + value;
  }

  function timeToDate(input, settings) {
    return (typeof input == 'object') ? normaliseTime(input) : timeStringToDate(input, settings);
  }

  function timeStringToDate(input, settings) {
    if (input) {
      var array = input.split(settings.separator);
      var hours = parseFloat(array[0]);
      var minutes = parseFloat(array[1]);

      // Convert AM/PM hour to 24-hour format.
      if (!settings.show24Hours) {
        if (hours === 12 && input.substr('AM') !== -1) {
          hours = 0;
        }
        else if (hours !== 12 && input.indexOf('PM') !== -1) {
          hours += 12;
        }
      }
      var time = new Date(0, 0, 0, hours, minutes, 0);
      return normaliseTime(time);
    }
    return null;
  }

  /* Normalise time object to a common date. */
  function normaliseTime(time) {
    time.setFullYear(2001);
    time.setMonth(0);
    time.setDate(0);
    return time;
  }

})(jQuery);

