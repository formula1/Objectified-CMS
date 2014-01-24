function castServerFunction(functionname, args, callback){
	var url = "/functions.php?funk="+functionname;
	for(i=0;i<args.length;i++)
		url += "&arg"+i+"="+args[i];

	jQuery.ajax(url).done(callback);
}


