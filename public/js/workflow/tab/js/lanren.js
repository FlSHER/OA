var indexCount=0;//记录当前索引位置
$(function(){
	var linksone=getClass("titleone")[0].getElementsByTagName("a");
	var listsone=getClass("listone");
	tab(linksone,listsone);
	if(0==indexCount)
	{
		$("#lastStep").attr("disabled","ture");
	}
	else
	{
		$("#lastStep").removeAttr("disabled");
	}
});
function tab(links,lists){
	for (var i=0; i<links.length; i++) {
    	links[i].index=i;
    	links[i].onclick=function() {
	  		for (var j=0; j<lists.length; j++){
	    		lists[j].style.display="none";
				links[j].style.background="";
				links[j].style.color="#000";
	  		}
      		lists[this.index].style.display="block";
	  		this.style.background="#fff";
	  		this.style.color="red";
	  		indexCount = this.index;//记录当前索引位置
	  		if(indexCount == links.length - 1)
	  		{
	  			$("#nextStep").attr("disabled","ture");
	  			$("#lastStep").removeAttr("disabled");
	  		}
	  		else if(indexCount == 0)
	  		{
	  			$("#lastStep").attr("disabled","ture");
	  			$("#nextStep").removeAttr("disabled");
	  		}
	  		else
	  		{
	  			$("#lastStep").removeAttr("disabled");
	  			$("#nextStep").removeAttr("disabled");
	  		}
    	}
  	}
}
//上一步
$('#lastStep').click(function(){
	var linksone=getClass("titleone")[0].getElementsByTagName("a");
	var listsone=getClass("listone");
	lastStep(linksone,listsone,indexCount);
	if(indexCount == 0)
	{
		$("#lastStep").attr("disabled","ture");
	}
	else
	{
		$("#lastStep").removeAttr("disabled");
		$("#nextStep").removeAttr("disabled");
	}
});
function lastStep(links,lists,curIndex){
	curIndex = curIndex - 1;
	for(var j=0; j<lists.length;j++)
	{
		lists[j].style.display="none";
		links[j].style.background="";
		links[j].style.color="#000";
	}
	links[curIndex].onclick();
	indexCount = curIndex;
}
//下一步
$('#nextStep').click(function(){
	var linksone=getClass("titleone")[0].getElementsByTagName("a");
	var listsone=getClass("listone");
	nextStep(linksone,listsone,indexCount);
	if(indexCount >= linksone.length-1)
	{
		$("#nextStep").attr("disabled","ture"); 
	}
	else
	{
		$("#lastStep").removeAttr("disabled");
	}
});
function nextStep(links,lists,curIndex){
	curIndex++;
	for(var j=0; j<lists.length;j++)
	{
		lists[j].style.display="none";
		links[j].style.background="";
		links[j].style.color="#000";
	}
	links[curIndex].onclick();
	indexCount = curIndex;
}