
	function initializeUploadProcess()
	{
		document.getElementById('loading').style.display = 'block';
		document.getElementById('result').style.display = 'none';
		
		return true;
	}
	 
	function finishUploadProcess(success,message)
	{
		var result = '';

		if (success == 1)
		{
			document.getElementById('result').innerHTML = message;
			document.getElementById('result').style.border = '1px solid green';
			document.getElementById('result').style.background = '#c7f7aa';					
		}
		else 
		{
			document.getElementById('result').innerHTML = message;
			document.getElementById('result').style.border = '1px solid red';
			document.getElementById('result').style.background = '#fab3ac';			
		}
		
			document.getElementById('result').style.display = 'block';
		
			document.getElementById('loading').style.display = 'none';
			
			return true;	  
    }
	
	function closeResult()
	{
		document.getElementById('result').style.display = 'none';
	}