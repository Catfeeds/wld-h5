function loadRegion(sel,type_id,selName,url){
	jQuery("#"+selName+" option").each(function(){ 
		jQuery(this).remove();
	});
	jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#"+selName));
	if(jQuery("#"+sel).val()==0){
		return;
	}	
	jQuery.getJSON(url,{parentid:jQuery("#"+sel).val(),regiontype:type_id},
		function(data){
		$("#district").html('<option value="" selected="selected">请选择</option>');
			if(data){
				
				jQuery.each(data,function(idx,item){					
					jQuery("<option value="+item.region_id+">"+item.region_name+"</option>").appendTo(jQuery("#"+selName));
					
				});
				
			}else{
				
				jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#"+selName));
				
			}
		}
	);
}

/*编辑个人其他资料信息加载地区信息*/
function loadcity(sel,type_id,selName,url){
	jQuery("#"+selName+" option").each(function(){ 
		jQuery(this).remove();
	});
	jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#"+selName));
	if(jQuery("#"+sel).val()==''){
		return;
	}	
	jQuery.getJSON(url,{parentid:jQuery("#"+sel+" option:selected").attr('id'),regiontype:type_id},
		function(data){
		$("#district").html('<option value="" selected="selected">请选择</option>');
			if(data){
				
				jQuery.each(data,function(idx,item){					
					jQuery("<option id="+item.region_id+" value="+item.region_name+">"+item.region_name+"</option>").appendTo(jQuery("#"+selName));
					
				});
				
			}else{
				
				jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#"+selName));
				
			}
		}
	);
}

/*2016-1-7-hhs-add-加载行业*/
function loadbusines(sel,selName,url)
{
	jQuery("#"+selName+" option").each(function(){ 
		jQuery(this).remove();
	});
	jQuery("<option value='0'>点击选择行业</option>").appendTo(jQuery("#"+selName));
	if(jQuery("#"+sel).val()==0){
		return;
	}	
	jQuery.getJSON(url,{business_type:jQuery("#"+sel).val()},
		function(data){
		$("#industrys").html('<option value="0">点击选择行业</option>');
			if(data){
				
				jQuery.each(data,function(idx,item){					
					jQuery("<option value="+item.c_id+">"+item.c_business_name+"</option>").appendTo(jQuery("#"+selName));
					
				});
				
			}else{
				
				jQuery("<option value='0'>点击选择行业</option>").appendTo(jQuery("#"+selName));
				
			}
		}
	);	
}