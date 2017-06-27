// check to see if input is whitespace only or empty
function isEmpty(val)
{
	if (val.match(/^s+$/) || val == "")
	{
		return true;
	}
	else
	{
		return false;
	}	
}

// check to see if input is number
function isNumber(val)
{
	if (isNaN(val))
	{
		return false;
	}
	else
	{
		return true;
	}	
}

// check to see if input is alphabetic
function isAlphabetic(val)
{
	if (val.match(/^[a-zA-Z]+$/))
	{
		return true;
	}
	else
	{
		return false;
	}	
}

// check to see if input is alphanumeric
function isAlphaNumeric(val)
{
	if (val.match(/^[a-zA-Z0-9]+$/))
	{
		return true;
	}
	else
	{
		return false;
	}	
}

//check to see if input is password
function isPassword(val)
{
	if (val.match(/^[a-zA-Z0-9!?:;.-_()]+$/))
	{
		return true;
	}
	else
	{
		return false;
	}	
}

// check to see if value is within range
function isWithinRange(val, min, max)
{
	if (val >= min && val <= max)
	{
		return true;
	}
	else
	{
		return false;
	}	
}

// check to see if input is a valid email address
function isEmailAddress(val)
{
	if (val.match(/^([a-zA-Z0-9])+([.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/))
	{
		return true;
	}
	else
	{
		return false;
	}	
}

// check to see if form value is checked
function isChecked(obj)
{
	if (obj.checked)
	{
		return true;
	}
	else
	{
		return false;
	}	
}

function Trim(TRIM_VALUE)
{
	if(TRIM_VALUE.length < 1)
	{
		return "";
	}

	TRIM_VALUE = RTrim(TRIM_VALUE);
	TRIM_VALUE = LTrim(TRIM_VALUE);
	if(TRIM_VALUE=="")
	{
		return "";
	}
	else
	{
		return TRIM_VALUE;
	}
} //End Function

function RTrim(VALUE)
{
	var w_space = String.fromCharCode(32);
	var v_length = VALUE.length;
	var strTemp = "";
	if(v_length < 0)
	{
		return "";
	}
	var iTemp = v_length -1;

	while(iTemp > -1)
	{
		if(VALUE.charAt(iTemp) == w_space)
		{
		}
		else
		{
			strTemp = VALUE.substring(0,iTemp +1);
			break;
		}
		iTemp = iTemp-1;

	} //End While
	return strTemp;

} //End Function

function LTrim(VALUE){
	var w_space = String.fromCharCode(32);
	if(v_length < 1)
	{
		return "";
	}
	var v_length = VALUE.length;
	var strTemp = "";
	
	var iTemp = 0;
	
	while(iTemp < v_length)
	{
		if(VALUE.charAt(iTemp) == w_space)
		{
		}
		else
		{
			strTemp = VALUE.substring(iTemp,v_length);
			break;
		}
		iTemp = iTemp + 1;
	} //End While
	return strTemp;
} //End Function

