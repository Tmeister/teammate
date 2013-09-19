(function($) {

$.fn.cicleSocials = function(options) {

  if (!this.length) { return this; }
  var opts = $.extend(true, {}, $.fn.cicleSocials.defaults, options);
  var angle;

  this.each(function() {
    var $this = $(this);
    $.each($('.circle .team-item', $this), function(index, val){
        $item = $(this);
        var $thum = $('.member-avatar', $item);
        var $icons = $('li', $item);
        var $social = $('.user-socials', $item);
        var increase = Math.PI * 2 / 20;
        var x = 0, y = 0, angle = 0;
        $icons.tooltip()
        switch($icons.length){
            case 8:
                angle = 3.61;
                break;
            case 7:
                angle = 3.8;
                break;
            case 6:
                angle = 3.95;
                break;
            case 5:
                angle = 4.1;
                break;
            case 4:
                angle = 4.24;
                break;
            case 3:
                angle = 4.4;
                break;
            case 2:
                angle = 4.54;
                break;
            case 1:
                angle = 4.7;
                break;
        }

        $item.hover(function() {
            $.each($icons, function(index, val) {
                var $icon = $(this);
                $icon.delay(50*(index+1)).animate({'opacity': '1'}, 250)
            })
        }, function() {
            $.each($icons, function(index, val) {
                var $icon = $(this);
                $icon.animate({'opacity': '0'}, 250)
            })
        });

        $.each($icons, function(index, val) {
            var $icon = $(this);
            x = 113 * Math.cos(angle) + 90;
            y = 113 * Math.sin(angle) + 90;
            $icon.css({'position': 'absolute', 'left' : x+'px', 'top': y+'px'});
            angle += increase;
        });
    });

    $.each($('.square .team-item', $this), function(index, val){
        $item = $(this);
        var $icons = $('li', $item);
        var $social = $('.user-socials', $item);
        var width = $icons.length * 31;
        console.log(width);
        $social.css({'left': $social.position().left + ( ($social.width() - width) / 2 )});
        $icons.tooltip();



        $item.hover(function() {
            $.each($icons, function(index, val) {
                var $icon = $(this);
                $icon.css('display', 'block').delay(50*(index+1)).animate({'opacity': '1'}, 250)
            })
        }, function() {
            $.each($icons, function(index, val) {
                var $icon = $(this);
                $icon.animate({'opacity': '0'}, 250)
            })
        });

    });

    $.each($('.card .team-item', $this), function(index, val){
        $item = $(this);
        var $icons = $('li', $item);
        $icons.tooltip();
    });



  });

  return this;
};

// default options
$.fn.cicleSocials.defaults = {};
})(jQuery);
