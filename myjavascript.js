function checkSignup() {
    if (!checkUsername(document.forms["signup"].elements["uname"].value)
        || !checkPassword(document.forms["signup"].elements["psw"].value)) {
        window.alert("Username or password not valid. \nUsername format: xxx@yyy.zz \n" +
            "Password must contain at least one lower case char and one between upper case char and digit");
        return false;   //block redirection
    }
    return true;    //allow redirection
}

function checkUsername(username) {
    firstSplit = username.split("@");
    if (firstSplit.length != 2) return false;
    secondSplit = firstSplit[1].split(".");
    if (secondSplit.length != 2) return false;
    return true;
}

function checkPassword(password) {
    var lower = 0;
    var upperOrDigit = 0;
    for (i = 0; i < password.length; i++) {
        c = password.charAt(i);
        if (parseInt(c) >= 0 && parseInt(c) <= 9)
            upperOrDigit++;
        else {
            //CHARACTER
            if (c == c.toLowerCase())
                lower++;
            if (c == c.toUpperCase())
                upperOrDigit++;
        }
    }
    if (lower > 0 && upperOrDigit > 0)
        return true;
    else
        return false;
}

function checkBookInput() {
    src1=document.forms["book"].elements["src1"].value;
    src2=document.forms["book"].elements["src2"].value;
    dst1=document.forms["book"].elements["dst1"].value;
    dst2=document.forms["book"].elements["dst2"].value;
    psng=document.forms["book"].elements["passengers"].value;
    if ((src1!='' && src2!='') || (dst1!='' && dst2!='')) {
        window.alert("Too much inputs, please select just one source and one destination");
        return false;   //block redirection
    }
    if ((src1=='' && src2=='') || (dst1=='' && dst2=='') || psng<=0) {
        window.alert("Too few inputs, please select one source, one destination and the number of passengers");
        return false;   //block redirection
    }
    src = (src1!='')? src1 : src2;
    dst = (dst1!='')? dst1 : dst2;
    if(src.localeCompare(dst)>=0){
        window.alert("Destination must follow source");
        return false;   //block redirection
    }
    return true;    //allow redirection
}

