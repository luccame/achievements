(function(a){function b(b){b.preventDefault();var c=a("#dpa-toolbar-filter").val(),d=a("#dpa-toolbar-search").val(),e=null,f="",g=a("#post-body-content > .current").prop("class");if(g.indexOf("grid")>=0){g="grid";f="#post-body-content > .grid a"}else if(g.indexOf("list")>=0){g="list";f="#post-body-content > .list tbody tr"}else if(g.indexOf("detail")>=0){g="detail";f="#post-body-content > .detail > ul li"}a(f).each(function(){e=a(this);"1"===c?e.hasClass("installed")?e.addClass("showme"):e.addClass("hideme"):"0"===c?e.hasClass("notinstalled")?e.addClass("showme"):e.addClass("hideme"):e.addClass("showme")});a(f+":not(.hideme)").each(function(){e=a(this);if("grid"===g&&e.children("img").prop("alt").search(new RegExp(d,"i"))<0||"list"===g&&e.children(".name").text().search(new RegExp(d,"i"))<0||"detail"===g&&e.prop("class").search(new RegExp(d,"i"))<0){e.removeClass("showme");e.addClass("hideme")}});a(f).each(function(){e=a(this);e.hasClass("showme")?e.show():e.hasClass("hideme")&&e.fadeOut();e.removeClass("hideme").removeClass("showme")});a.cookie("dpa_sp_filter",c,{path:"/"})}function c(c,d){c=a.trim(c);a("#post-body-content > .current, #dpa-toolbar-views li.current").removeClass("current");a("#post-body-content > ."+c).addClass("current");a("#dpa-toolbar-views li a."+c).parent().addClass("current");b(d);a.cookie("dpa_sp_view",c,{path:"/"})}function d(b){var c=b.prop("class");c=c.substr(0,c.indexOf(" "));a("#post-body-content > .detail > ul li").removeClass("current");b.addClass("current");a("#dpa-detail-contents > div").removeClass("current");a("#dpa-detail-contents ."+c).addClass("current");a.cookie("dpa_sp_lastplugin",c,{path:"/"})}a(document).ready(function(){a("#post-body-content > .detail > ul li").on("click.achievements",function(b){b.preventDefault();d(a(this))});a("#post-body-content > .list .plugin img").on("click.achievements",function(b){b.preventDefault();c("detail",b);d(a("#post-body-content > .detail > ul li."+a(this).prop("class")))});a("#post-body-content > .grid a").on("click.achievements",function(b){b.preventDefault();c("detail",b);d(a("#post-body-content > .detail > ul li."+a(this).children("img").prop("class")))});a("#dpa-toolbar-wrapper li a").on("click.achievements",function(b){b.preventDefault();a(this).hasClass("current")||c(a(this).prop("class"),b)});a("#dpa-toolbar-filter").on("change.achievements",b);a("#dpa-toolbar-search").on("keyup.achievements",b)})})(jQuery);