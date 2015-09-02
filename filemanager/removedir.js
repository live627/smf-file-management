/* ******************************************************************************************* removedir.js														  	  ** The SMF File Manager modification by Marcus "Nas" Forsberg.							  ** This file was last updated in verison 1.0 RC1 Build 2.									  ** This modification, including this file, is copyrighted and may not be redistributed.				  ** Copyright (c) 2009 Marcus Forsberg											  ******************************************************************************************** *//* 	This file handles the effect upon removal of a directory.		function initializeRemovalProcess()				- Display a message telling them the process has begun.		function displayMessage(message to display)				- Tell them we are removing something.		function finishRemovalProcess(message to display)				- Tell them we're done.			- Let them know if we succeeded or not.			*/	function initializeRemovalProcess()
	{
		document.getElementById('start').style.display = 'block';		return true;
	}
	function displayMessage(message)
	{
		var actions = document.getElementById('actions');		var old = getinnerHTML('actions');			
		setInnerHTML(actions,old.message);
		return true;	  
    }	
	function finishRemovalProcess(message)	{		var finish = document.getElementById('finish');				document.getElementById(finish).style.display = 'block';		setInnerHTML(finish,message);		return true;	}