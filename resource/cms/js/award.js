var awardData;
$.ajax({
    url: 'award.php',
    processData: false,
    contentType: false,
    type: 'GET',
    dataType:'json',
    success:function(data){
		if (data.status=='success') {
			awardData = data.data;
			$.each(data.data,function(key,value){ 
			    var value_str=JSON.stringify(value); 
			    if (value.flag=='0') {
			    	value.flag='否';
			    }else if (value.flag=='1') {
			    	value.flag='是';
			    } 
			    key=key+1;        	
            	var code="<td class='list_code'>"+key+"</td>";
            	var name="<td class='list_name'>"+value.name+"</td>";
            	var img="<td class='list_pic'><img src='../"+value.img+"'></td>";
            	var chance="<td class='list_common'>"+value.chance+"</td>";
            	var flag="<td class='list_common'>"+value.flag+"</td>";
            	var total="<td class='list_common'>"+value.total+"</td>";
            	var give_out="<td class='list_common'>"+value.give_out+"</td>";
            	var edit="<td class='list_edit' onclick='showDetail("+(key-1)+")'>编辑</td>";
            	var content = '<tr>'+code+name+img+flag+chance+total+give_out+edit+'</tr>';
            	$('#award_list table').append(content);
			});
		}else{
			console.log(data);
			alert('数据获取失败');
		}
	}
});
var id;
function showDetail(awardId){
	var award = awardData[awardId];
	id = award.id;
	$('#award_list').hide();
	$('#award_edit').show();
	var height = document.getElementById("right").offsetHeight;
	$('#cover').css('height',height);
	$('#left').css('height',height);
	$("input[name='name']").val(award.name);
	$("input[name='detail']").val(award.detail);
	$("#textarea").val(award.desc);
	$("input[name='chance']").val(award.chance);
	$("input[name='total']").val(award.total);
	$("#have_award select").val(award.flag);
	$('#award_img img').attr('src','../'+award.img);
	$('#award_rule').val(award.get_rule);
	$("input[name='get_url']").val(award.get_url);
}
function changePic(source){
	var file = source.files[0];
	if(window.FileReader) {
		var fr = new FileReader();
		fr.onloadend = function(e) {
		    console.log(e.target.result);
		    $('#award_img img').attr('src',e.target.result);
		};
		fr.readAsDataURL(file);
	}
}
function saveEdit(){
    var data = new FormData($('#form_edit')[0]); 
    data.append('id',id);
    $.ajax({  
        url: 'award.php',  
        type: 'POST',  
        data: data,  
        dataType: 'JSON',  
        cache: false,  
        processData: false,  
        contentType: false  
    }).done(function(ret){  
        if (ret.status=='success') {
    		location.reload();
        } else{
        	alert('上传失败');
        } 
    }); 
}
function cancel(){
	$('#award_list').show();
	$('#award_edit').hide();
	var height = document.getElementById("right").offsetHeight;
	$('#cover').css('height',height);
	$('#left').css('height',height);
}












