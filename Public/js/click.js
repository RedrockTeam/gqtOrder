/*
* @Author: 10261
* @Date:   2017-02-18 22:23:08
* @Last Modified by:   10261
* @Last Modified time: 2017-02-20 00:27:43
*/

'use strict';

function mark (dom) {
	return document.querySelector(dom);
}

function $$ (dom) {
	return document.querySelectorAll(dom);
}

var checkArray = [];
var flag = 0;
var checkCount = 0;

function clickMore() {
	var moreAll = $$(".more");
	var projectDetAll = $$(".projectDet");
	for(var i = 0; i < moreAll.length; i++) {
		(function (j) {
			moreAll[j].addEventListener('click', function () {
				if (moreAll[j].innerHTML == "点击展开") {
					projectDetAll[j].style.height = "3.6rem";
					moreAll[j].innerHTML = "点击收起";
				} else {
					projectDetAll[j].style.height = "1.8rem";
					moreAll[j].innerHTML = "点击展开";
				}
			})
		})(i)
	}
}

function alertYou (state, text) {
	
	var alertBox = mark("#alertBox");
	var stateImg = mark("#stateImg");
	var lineOne = mark(".lineOne");
	var lineTwo = mark(".lineTwo");

	switch (state) {
		case 1: {
			stateImg.src = publicPath + "/img/wrong.png";
			mark(".lineOne").style.display = "block";
			mark(".lineTwo").style.marginTop = "0";
			lineOne.innerHTML = "对不起";
			lineTwo.innerHTML = "您还未进行勾选";
			flag = 0;
			break;
		}
		case 2: {
			stateImg.src = publicPath + "/img/wrong.png";
			lineOne.innerHTML = "住手";
			mark(".lineOne").style.display = "block";
			mark(".lineTwo").style.marginTop = "0";
			lineTwo.innerHTML = "您已经选满3项了";
			flag = 0;
			break;
		}
		case 3: {
			stateImg.src = publicPath + "/img/wrong.png";
			mark(".lineOne").style.display = "none";
			mark(".lineTwo").style.marginTop = "0.3rem";
			lineTwo.innerHTML = "亲，信息不能为空哟~";
			flag = 0;
			break;
		}
		case 4: {
			stateImg.src = publicPath + "/img/success.png";
			lineOne.innerHTML = "点单成功！";
			mark(".lineOne").style.display = "block";
			mark(".lineTwo").style.marginTop = "0";
			lineTwo.innerHTML = "撸起袖子加油干吧！";
			flag = 1;
			break;
		}
		case 5: {
			stateImg.src = publicPath + "/img/wrong.png";
			lineOne.innerHTML = "对不起";
			mark(".lineOne").style.display = "block";
			mark(".lineTwo").style.marginTop = "0";
			lineTwo.innerHTML = text;
			flag = 0;
			break;
		}
		default: break;
	}

	alertBox.style.display = "block";

}

function judge () {
	var checkBoxAll = $$(".checkBoxA");
	var checkAll = $$("input[type=checkbox]");
	for (var i = 0; i <checkBoxAll.length; i ++) {
		(function (j) {
			var count = 0;
			var xFlag = 0;
			checkBoxAll[j].addEventListener('click', function () {
				console.log(checkAll[j].checked);
				if (checkAll[j].checked == false) {
					count--;
				} else {
				    count++;
				    if (xFlag == 1) {
				    	checkAll[j].checked = false;
				    	xFlag = 0;
				    }
				}
				if (count == -1) {
					checkCount++;
					if (checkCount == 4) {
						alertYou(2);
						checkCount = 3;
						xFlag = 1;
					}
				} else if (count == 1) {
					checkCount--;
				}
				console.log(xFlag);
			});
		})(i);
	}
}

function clickAll () {
	
	var clickYes = mark("#clickYes");

	clickYes.addEventListener('click', function () {
		
		var allCheck = $$("input[type=checkbox]");

		checkArray = [];

		for(var i = 0; i < allCheck.length; i ++) {
			if (allCheck[i].checked) {
				checkArray.push(allCheck[i].value);
			}
		}

		if (checkArray.length == 0) {
			alertYou(1);
		} else if (checkArray.length >3) {
			alertYou(2);
		} else {
			console.log(checkArray);
			mark("#confirmBox").style.display = "block";
		}

	})

}

function confirmSubmit () {
	var confirmYes = mark("#confirmYes");

	confirmYes.addEventListener('click', function () {
		var user = new Object;
		var myInput = $$(".myIput");

		for (var i = 0; i < myInput.length; i ++) {
			switch (myInput[i].name) {
				case ("company"): {
					user.company = myInput[i].value;
					break;
				}
				case ("username"): {
					user.name = myInput[i].value;
					break;
				}
				case ("phone"): {
					user.phone = myInput[i].value;
					break;
				}
				default: break;
			}
		} 
		
		if (user.company && user.name && user.phone && checkArray !== []) {
			user.select = checkArray;
			console.log(user);
			$.ajax({
				type: "POST",
				data: user,
				url: "http://hongyan.cqupt.edu.cn/gqtOrder/Home/Index/order",
				success: function (data) {
					if (data.status == 200) {
						alertYou(4);
					} else {
						alertYou(5, data.info);
					}
					console.log(data);
				},
				error: function (data) {
					console.log(data);
				}
			})
		} else {
			alertYou(3);
		}

	})
}

function start () {
	
	checkCount = 0;

	clickAll();
	confirmSubmit();
	clickMore();
	judge();

	mark("#stateYes").addEventListener('click', function () {
		console.log(flag);
		if (!flag) {
			mark("#alertBox").style.display = "none";
		} else {
			window.location.href = morePath;
		}
	});

	mark("#back").addEventListener('click', function () {
		mark("#confirmBox").style.display = "none";
	});

}

start();