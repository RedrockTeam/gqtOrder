/*
* @Author: 10261
* @Date:   2017-02-19 16:12:03
* @Last Modified by:   10261
* @Last Modified time: 2017-02-19 16:12:08
*/

'use strict';
function ajax(opts){
     var defaults = {    
             method: 'GET',
                url: '',
               data: '',        
              async: true,
              cache: true,
        contentType: 'application/x-www-form-urlencoded',
            success: function (){},
              error: function (){}
         };    
  
     for(var key in opts){
         defaults[key] = opts[key];
     }
 
     if(typeof defaults.data === 'object'){    //处理 data
         var str = '';
         for(var key in defaults.data){
             str += key + '=' + defaults.data[key] + '&';
         }
         defaults.data = str.substring(0, str.length - 1);
     }
 
     defaults.method = defaults.method.toUpperCase();    //处理 method
 
     defaults.cache = defaults.cache ? '' : '&' + new Date().getTime() ;//处理 cache
 
     if(defaults.method === 'GET' && (defaults.data || defaults.cache))    defaults.url += '?' + defaults.data + defaults.cache;    //处理 url    
     


     //1.创建ajax对象
     var oXhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
     //2.和服务器建立联系，告诉服务器你要取什么文件
     oXhr.open(defaults.method, defaults.url, defaults.async);
     //3.发送请求
     if(defaults.method === 'GET')    
         oXhr.send(null);
     else{
         oXhr.setRequestHeader("Content-type", defaults.contentType);
         oXhr.send(defaults.data);
     }    
     //4.等待服务器回应
     oXhr.onreadystatechange = function (){
         if(oXhr.readyState === 4){
             if(oXhr.status === 200)
                 {console.log("OK");
                 defaults.success.call(oXhr, oXhr.responseText);}
             else {
                 defaults.error();
             }
         }
     };
 }
