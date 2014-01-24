<?php 
include "../../../genericfunk.php";
$url = getURL(__file__);





?>

<canvas id="spiral" width="256" height="256"></canvas>
<script type="text/javascript">

function move(){
	var img = new Image();
	sx.translate(-1 + final_radius,-1 + final_radius);
	sx.rotate(Math.PI*2/(12 * Math.pow(2,4)));
	sx.translate(1 - final_radius,1 - final_radius);
	drawSpi();
	sx.translate(-1 + final_radius,-1 + final_radius);
	sx.translate(1 - final_radius,1 - final_radius);
	setTimeout(function(){move();},10);
}

var spiralcanv = document.getElementById("spiral");
var final_radius = 128;
var sx=spiralcanv.getContext("2d");

var quarter = Math.PI/2;
var third = Math.PI*2/3;
var full = Math.PI*2;
var gold_ratio = (1+Math.sqrt(5))/2;

var radius=1;
var starting_ang = 0;
var ending_ang = quarter;


colors = [
	"#FFFF00",	//yellow
//	"#FF7800",	//orange
	"#FF0000",	//red
//	"#FF00FF", 	//purp
	"#0000FF",	//blue
//	"#00FF00"	//green
];

function drawSpi(){
sx.translate(-1 + final_radius,-1 + final_radius);

for(var i=2;i>=0;i--){


	sx.rotate(third);
	sx.strokeStyle = colors[i];


		radius=1;
		spicen = [0,0];
		starting_ang = 0;//i*quarter%full;
		ending_ang = quarter;//(i+1)*quarter%full;

		while(radius < final_radius*2){
			sx.beginPath();
			sx.arc(spicen[0],spicen[1],radius,starting_ang,ending_ang);
			
			old_r = radius;
			radius = radius*gold_ratio;
			spicen[0] += -1*Math.cos(ending_ang)*(radius-old_r);
			spicen[1] += -1*Math.sin(ending_ang)*(radius-old_r);
			starting_ang = (starting_ang +quarter)%full;
			ending_ang = (ending_ang+quarter)%full;

			sx.rotate(-third);

			sx.arc(spicen[0],spicen[1],radius,ending_ang,starting_ang,true);
			sx.rotate(third);
			sx.closePath()
			sx.stroke();
			sx.fillStyle = colors[i];
			sx.fill();		
		}

}
sx.globalAlpha = 1;

var grd=sx.createRadialGradient(0,0,1,0,0,final_radius);
grd.addColorStop(0,'rgba(255,255,255,1)');
grd.addColorStop(.495,'rgba(255,255,255,0)');
grd.addColorStop(.505,'rgba(255,255,255,0)');
grd.addColorStop(1,'rgba(255,255,255,1)');
sx.beginPath();
sx.fillStyle = grd;
sx.arc(0, 0, final_radius*2, 0, Math.PI*2, false);
	sx.stroke();

sx.fill();

sx.translate(1 - final_radius,1 - final_radius);

}

move();
/*var img = jQuery("<img />");
img.attr("src",spiralcanv.toDataURL());
jQuery("#spiral").parent().append(img);
*/
</script>