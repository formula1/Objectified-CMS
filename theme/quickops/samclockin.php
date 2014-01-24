<?php

include_once "applications/clockin/classes/classes.php";

class SamsClockIn{
	private $loggedin = false;
	private $clockedin = null;

	public function __construct(){
		$user = User::find(array("email"=>"samtobia@gmail.com"));
		
		$user = $user[0];
		
		$working = WorkerBoot::findByUserID($user->ID);
		
		
		$this->loggedin = ($user->loggedin)?true:false;
		$this->clockedin = ($working->current_clockin != null)?new ClockIn($working->current_clockin):null;
	}

	public function getJSON(){
	
	}
	
	public function getHTML(){	
	?>
	<ul class="vertical">
		<li>Sam is <?php echo ($this->loggedin)?"":"not"; ?> currently available.</li>
		<li><?php if($this->loggedin) { ?><a>Chat</a> with him live
		<?php }else{ ?>Leave him a <a>message</a><?php } ?></li>
		<li>See what he <?php echo ($this->clockedin != null)?"is":"was"; ?> <a href="Data/WorkerBoot/<?php echo $this->clockedin->user;?>.html" >Working</a> on</li>
		<li><a>Learn</a> about Sam<br /></li>
	</ul>
<?php
	}
}


?>