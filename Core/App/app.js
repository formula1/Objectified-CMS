/*

Now an application has a variaty of concepts
1) background tasks
	-Just because the dialogs are closed doesn't mean the background tasks are done
	-Make sure that the person is aware background tasks are taking place though
2) On close
	-for clockin, it needs to clock them out as well
	-Generally, close all dialogs, stop background tasks
3) On logout
	-for clockin, it needs to clock them out as well
	-Generally, refresh the page
4) On Login
	-Generally just refresh the page
5) On open
	-return whats gotten from app.php




*/


function app(){
	registerBackgroundTask(type,callback){
		/*
			background tasks can be a variety of things
				1) webworker - primarilly web sockets and recursive polling
				2) recursive setTimeout - primarilly gui aspects
				
			I think when I hit that point where I am noticing a lot of wet code, I can change it;
		*/
	}

}