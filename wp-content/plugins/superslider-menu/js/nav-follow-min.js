var navArrowFollow=function(wrapper,navElemArray,activeID,arrowX,y0ffset,speed){var yerHere=new Fx.Tween($(wrapper),{duration:speed,transition:Fx.Transitions.Sine.easeInOut});$$(navElemArray).each(function(item){item.addEvent("mouseenter",function(){var thisPos=item.getPosition(wrapper).y+item.getSize().y-y0ffset;yerHere.cancel();yerHere.start("background-position",arrowX+"px "+thisPos+"px")})});var curArrow=function(){yerHere.cancel();var actPos=$(activeID).getPosition(wrapper).y+$(activeID).getSize().y-y0ffset;yerHere.start("background-position",arrowX+"px "+actPos+"px")};var actPos=$(activeID).getPosition(wrapper).y+$(activeID).getSize().y-y0ffset;$(wrapper).setStyle("background-position",arrowX+"px "+actPos+"px");curArrow();$(wrapper).addEvent("mouseleave",curArrow)};