var ajx;

function ajaxify(frame_creator){

var that = this;
var fc = frame_creator;
var hrefs = [
		"/",
		"localhost",
		"www.localhost",
		"http://www.localhost",
		"https://www.localhost",
		"http://localhost",
		"https://localhost"
	];
	
var anchor_selector = 'a[href^="'+hrefs.join('"], a[href^="') + '"]';
var form_selector = 'form[action^="'+hrefs.join('"], form[action^="') + '"]';


	this.shiftScene = function(frame,settings){
		$.ajax(settings)
		.done(function(content){
			if(typeof frame == "undefined") console.log(content);
			if(typeof frame == "function") frame(content);
			else{
				var con = $(content);
				that.applyChange(con);
				frame.append(con);
			}
		});
	
	};
	
	this.applyChange = function(content){
	
	
		console.log("forms: "+$(content).find("form").size());
	
		var fs_and_as = $(content).find(anchor_selector+", "+form_selector);
		
		console.log("found: "+fs_and_as.size());

		var anchors = fs_and_as.filter("a");
		var forms = fs_and_as.filter("form");

		console.log("formed" + forms.size());
		
		anchors.click(function(e){
			e.preventDefault();
			var article = $(this);
			var settings = {};
			settings.url = article.attr("href");
			settings.method = "GET";
			psuedoframe = getOurFrame(article, settings.url);
			that.shiftScene(psuedoframe, settings);
		});

		forms.submit(function(e){
			e.preventDefault();
			var article = $(this);
			var settings = {};
			settings.url = article.attr("action");
			settings.method = (typeof article.attr("method") == "undefined" || article.attr("method") == "")?"GET":article.attr("method");
			if(settings.method == "GET") settings.url += "?"+article.serialize();

			psuedoframe = getOurFrame(article, settings.href);
			that.shiftScene(psuedoframe, settings);
		});
	
		function getOurFrame(article, href){
			var ourframe, bigboy;
			console.log("target: "+article.attr("target"));
			if(article.attr("target")=="_blank" || article.attr("target")=="blank"){
				ourframe = $("<div class='psuedo_frame'></div>");
				bigboy = fc.processFrame(ourframe);
				$("body .psuedo_frame:first").append(bigboy);
			}else if(article.attr("target") == "_self" || article.attr("target")=="self" || typeof article.attr("target")=="undefined" || article.attr("target")==""){
				console.log("parent");
				ourframe = article.parents("div.psuedo_frame");
			}else if(article.attr("target") == "_top" || article.attr("target")=="top"){
				ourframe = $("body .psuedo_frame:first");
			}else if(article.attr("target") == "_none" || article.attr("target")=="none"){
				return;
			}else if(article.attr("target") == "function"){
				return eval(article.attr("data-function"));
			}else if((ourframe = $("body .psuedo_frame:first").find('[data-framename="'+article.attr("target")+'"]')).size() == 0){
				ourframe = $("<div class='psuedo_frame' data-framename='"+article.attr("target")+"'></div>");
				bigboy = fc.processFrame(ourframe);
				$("body .psuedo_frame:first").append(bigboy);
			}
			ourframe.empty();
			ourframe.attr("data-src", href);
			
			return ourframe;

		
		}
	}

}

function frame_creator(){
var mouse = {};



		this.processFrame = function(psuedoframe){
			var contain = $('<div class="dialog"><header><a class="close" style="background-color:#FFF;float:right" href="#">Close</a></header><section class="content"></section></div>');
		
			contain.find("header").mousedown(function(e){
				e.preventDefault();
				mouse.x = e.clientX;
				mouse.y = e.clientY;
				$(".dialog:not(.top)").css("z-index",2);
				$(this).parent().css("z-index",4);
				$("body").mousemove(function(e2){
					var diff = {x:e2.clientX - mouse.x, y:e2.clientY - mouse.y};
					var curpos = contain.position();
					newx = 	Math.max(Math.min((curpos.left+diff.x), contain.parent().width() - contain.width() ) ,0);
					newy = 	Math.max(Math.min((curpos.top+diff.y), contain.parent().height() - contain.height() ) ,0);
					mouse = {x: e2.clientX, y: e2.clientY};
					contain.css({left: newx+"px", top: newy+"px" });
				});
				$("body").mouseup(function(e){
					e.preventDefault();
					$("body").unbind("mousemove");
					$("body").unbind("mouseup");
				});
			});
			contain.find(".close").click(function(e){
				e.preventDefault();
				contain.remove();
			});
			contain.find("section").append(psuedoframe);
		return contain;

		}

}

jQuery(function($){


	ajx = new ajaxify(new frame_creator());
	ajx.applyChange($("body"));
	

	
});