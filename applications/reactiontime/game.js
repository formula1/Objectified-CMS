function game(){
	var that = this;
	var gameboard;
	var numberelems=0;
	var wins = 0;
	var over = true;
	var currentXY = {};
	var postext;
	
	this.win = function(difficulty){
		over = true;
		gameboard.empty();
		gameboard.append("<h1>Tell me when your ready for Level "+(difficulty+1)+"</h1>");
		var start = $("<a href=\"#\" style=\"font-size:30px\">Start</a>");
		start.click(function(e){
			e.preventDefault();
			that.level(difficulty+1);
		});
		gameboard.append(start);
	};
	
	this.lose = function(){
		over = true;
		gameboard.empty();
		gameboard.append("<h1>Sorry you lost :C</h1>");
		var start = $("<a href=\"#\" style=\"font-size:30px\">Start</a>");
		start.click(function(e){
			e.preventDefault();
			that.level(1);
		});
		gameboard.append(start);

		
	};

	this.createElement = function(difficulty){

	
		var totaldifficulty = 0;
		var div = {h:gameboard.height(),w:gameboard.width()};
		
	
		//------------------------GET CENTER POIINT------------------------------
		center = {x:Math.floor(Math.random()*div.w), y:Math.floor(Math.random()*div.h)};
		var msdis = Math.pow(Math.pow(currentXY.x-center.x,2)+Math.pow(currentXY.y-center.y,2), 1/2);
		var MaximumDistance = Math.pow(Math.pow(div.h,2) + Math.pow(div.w,2), 1/2);
		
		totaldifficulty += msdis/MaximumDistance;

		var diff = {}
		//-------------------------CALCULATING SIZE----------------------
		diff.x = Math.min(div.w - center.x, center.x);
		diff.y = Math.min(div.h - center.y, center.y);
		
		console.log(JSON.stringify(diff));
		
		var radius = 1+Math.random() * Math.min(diff.x, diff.y);
		var maxradius = Math.min(div.w/2, div.h/2);
		
		//here, the greater the radius, the lower the difficulty
		totaldifficulty /= radius/maxradius;
		console.log(totaldifficulty);
		var createtime = 250*(numberelems) + 10*totaldifficulty; //10000*(.5+Math.random()+(numberelems+totaldifficulty)/difficulty)/3

		var killtime = 1000*(numberelems+totaldifficulty) +1000/difficulty - createtime; //10000*(.5+Math.random()+(numberelems+totaldifficulty)/difficulty)/3;
		console.log(killtime);

		var style = "background-color:#000;";
		style += "-webkit-border-radius:"+radius+"px;-moz-border-radius:"+radius+"px;border-radius: "+radius+"px;";
		style += "display:block;width:"+radius*2+"px;height:"+radius*2+"px;";
		style += "position:absolute;left:"+(center.x-radius)+"px;top:"+(center.y-radius)+"px;";
		
		var boo = true; //check if hit
		var elem = $('<a style="'+style+'" href="#" ></a>');
		elem.click(function(e){
			e.preventDefault();

			boo = false;
			$(this).remove();
			numberelems-= 1;
			wins++;
			if(wins === 10) that.win(difficulty);
			else that.createElement(difficulty);
		});
		setTimeout(function(){
			if(over) return;
			numberelems += 1;
			gameboard.append(elem);
			elem.fadeTo(killtime, 0, function(){
				if(boo && !over) that.lose();
			});
		}, createtime);
	};
	
	this.level = function(difficulty){
		over = false;
		numberelems=0;
		wins = 0;

		if(gameboard){
			gameboard.empty();
		}else{
			$("#rc").empty();
			gameboard = $("<div class=\"gameboard\" style=\"position:relative;width:100%;height:100%;\"></div>");
	//		postext = $("<h1 style=\"z-index:5;position:absolute;bottom:0px;right:0px\"></h1>");
			gameboard.mousemove(function(e){
				currentXY = {x:e.offsetX,y:e.offsetY};
	//			postext.html("x:"+e.offsetX+", y:"+e.offsetY);
			});
			$("#rc").append(gameboard);
		}
	//	gameboard.append(postext);
		gameboard.append("<h1 class=\"timer\">3</h1>");

		setTimeout(function(){
			$("#rc .timer").html("2");
			setTimeout(function(){
				$("#rc .timer").html("1");
					setTimeout(function(){
						$("#rc .timer").html("GO!");
						$("#rc .timer").fadeTo(1000,0,function(){
							$("#rc .timer").remove();
						});
						for(var i=0;i<difficulty;i++)
							that.createElement(difficulty);
					}, 1000);
			}, 1000);
		}, 1000);
	};


	var calculatefrommousepoint = function(){

		//---------------CALCULATING DISTANCE FROM CURRENT MOUSE LOCTAION---------------------

		var MaximumDistance = Math.pow(Math.pow(div.h,2) + Math.pow(div.w,2), 1/2);


		var angleA = Math.PI*2*Math.random();
		var percxy = {x:Math.cos(angleA),y:Math.sin(angleA)};


		var  diff = {};
		diff.x = (percxy.x > 0)? div.w - currentXY.x: currentXY.x;
		diff.y = (percxy.y > 0)? div.h - currentXY.y: currentXY.y;



		var angleB = pih - angleA%(piq);

		var lengthbasedY = diff.y/Math.sin(angleA)*Math.sin(pih); //law of sins :) enjoying math definitely helps remembering things like this
		var lengthbasedX = diff.x/Math.sin(angleB)*Math.sin(pih);


		var center = {};
		var newHyp = Math.random() * Math.min(lengthbasedX, lengthbasedY);



		center = {x:percxy.x*newHyp + currentXY.x, y:percxy.y*newHyp + currentXY.y};

		totaldifficulty += newHyp/MaximumDistance; //here the greater the difference, the greater the difficulty

	}


}

