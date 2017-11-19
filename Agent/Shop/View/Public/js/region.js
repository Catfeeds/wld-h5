	/*获取省市区信息*/
	function loadRegion(sel, type_id, selName, url) {
		jQuery("#" + selName + " option").each(function() {
			jQuery(this).remove();
		});
		//jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#"+selName));
		if (jQuery("#" + sel).val() == 0) {
			return;
		}
		jQuery.getJSON(url, {
				parentid: jQuery("#" + sel).val(),
				regiontype: type_id
			},
			function(data) {
				//$("#district").html('<option value="" selected="selected">请选择</option>');
				if (data) {
					jQuery.each(data, function(idx, item) {
						if (item.region_name == citye && selName == "city") {
							jQuery("<option value=" + item.region_id + " selected=\"selected\">" + item.region_name + "</option>").appendTo(jQuery("#" + selName));
						} else if (item.region_name == newdistrict && selName == "district") {
							jQuery("<option value=" + item.region_id + " selected=\"selected\">" + item.region_name + "</option>").appendTo(jQuery("#" + selName));
						} else {
							jQuery("<option value=" + item.region_id + ">" + item.region_name + "</option>").appendTo(jQuery("#" + selName));
						}

					});
					if (selName == "city") {
						loadRegion('city', 3, 'district', "<?php echo U('Getbusiness/getRegion');?>");
					}
				} else {

					jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#" + selName));

				}
			}
		);
	}

