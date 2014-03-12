if (typeof jQuery == 'undefined') {   
	var head = document.getElementsByTagName("head")[0]; 
	script = document.createElement('script'); 
	script.id = 'jQuery'; 
	script.type = 'text/javascript'; 
	script.src = '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'; 
	head.appendChild(script); 
}