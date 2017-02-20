/*
* @Author: 10261
* @Date:   2017-02-19 02:59:06
* @Last Modified by:   10261
* @Last Modified time: 2017-02-20 02:11:13
*/

'use strict';
var det = [
    "开展“学习习总书记讲话，强化共青团员意识”主题教育实践活动，围绕党的十九大、建团95周年、建军90周年、市第五次党代会、重庆直辖20周年开展主题报告、专题宣讲、青春故事分享、团队仪式教育、网络主题活动",
    "“总有一种感动，让我们的青春泪流满面”文化产品生产及推广",
    "青年马克思主义者培养工程",
    "“青年之声”互动社交平台建设",
    "“常春藤”网络舆论引导青年突击队建设",
    "未来企业家培养青锋计划，贫困大学生创业帮扶“青禾工程”，农村青年电商培育工程",
    "参与“创青春”青年创新创业大赛、“五小”创新晒活动",
    "城乡社区市民学校深化“四点半课堂” “青少年安全自护教育”服务品牌项目，参与创“星”活动",
    "“希望工程·圆梦行动”“冬日阳光·温暖你我”新春关爱活动",
    "“山茶花”扶贫攻坚青年突击队建设",
    "“山城雪豹”抢险救灾青年突击队建设",
    "实施“彩虹帮教”，推行“行为矫正+职业教育+就业融入”三步帮教模式",
    "12355青少年服务台“台—站—室”三级服务体系建设和社会化维权队伍、青少年维权岗创建",
    "“共青团与人大代表、政协委员面对面”活动",
    "青年社会组织骨干、青少年事务社工培养，推动政府购买青少年事务社会工作服务",
    "团支部书记队伍建设",
    "机关开放日活动、大宣传大调研活动",
    "团员发展调控工作，落实“入团先当志愿者”要求，加强“推优入党”",
    "群团服务站建设"
];

var length = {
	current: 0
};

function mark (dom) {
	return document.querySelector(dom);
}

function $$ (dom) {
	return document.querySelectorAll(dom);
}

function addPost (data) {
	var a = document.createElement("div");
	var b = document.createElement("div");
	var c = document.createElement("div");
	a.className = "postClass";
	b.className = "post";
	c.className = "postNum";
	b.innerHTML = data.company;
	c.innerHTML = data.select.join('、');
	console.log(data.select.join('、'));
	a.appendChild(b);
	a.appendChild(c);
	mark("#moreBox").appendChild(a);
}

function addDet (se) {
	var a = document.createElement("div");
	a.className = "list";
	a.innerHTML = "<span>" + se + "、</span>" + det[parseInt(se) - 1] + '。';
	mark("#yourList").appendChild(a);
}

function getInfo () {
	ajax({
		method: "POST",
		data: length,
		url: "http://hongyan.cqupt.edu.cn/gqtOrder/Home/Index/orderList",
		success: function (data) {
			data = JSON.parse(data).data;
			for (var i = 0; i < data.length; i ++) {
				addPost(data[i]);
				showDet();
			}
			length.current += data.length;
			if (length.current >= 10) {
			   mark("#insideB").style.display = "block";
		       mark("#insideF").style.display = "block";
		       mark("#warpB").style.display = "none";
		       mark("#warpF").style.display = "none";
			}
		},
		error: function (data) {
			console.log("er");
		}
	})
	console.log(1);
}

function page() {
	var close = mark("#close");
	var backOne = mark("#warpB");
	var backTwo = mark("#insideB");

	close.addEventListener('click', function () {
		mark("#alertBox").style.display = "none";
	});

	backOne.addEventListener('click', function () {
		window.location.href = indexPath;
	});
	backTwo.addEventListener('click', function () {
		window.location.href = indexPath;
	});
};


function showDet () {
	var postClass = $$(".postClass");
	var postNum = $$(".postNum");
	var alertBox = mark("#alertBox");
	for (var i = 0; i < postClass.length; i ++) {
		(function (j) {
			postClass[j].addEventListener('click', function () {
				var select = new Array();
				select = postNum[j].innerHTML.split('、');
				console.log(select);
				mark("#yourList").innerHTML = "";
				for (var m = 0; m < select.length; m ++ ) {
					addDet(select[m]);
				}
				mark("#alertBox").style.display = "block";
			})
		})(i);
	}
}

function start() {
	page();
	getInfo();
	setInterval(getInfo, 5000);
}

start();