function hideen(a) {
	  b = "file_" + a;
	  c = "show_" + a;
	  f = "fileList_" + a;
	  e = "fileElem_" + a;
	  tu = document.getElementById(b);
	  show = document.getElementById(c).innerHTML = '';
	  addimg = document.getElementById(f);
	  tu.style.display = 'none';
	  addimg.style.display = 'block';
	  fileImg=document.getElementById(e);
	  w = "tmimg_"+a;
	  timgs=document.getElementById(w).value;
	  if(timgs != ''){
		  addimg.style.display='block'; 
	  }
	  fileImg.value = "";
	  $('#img_sign').val(0);
	
	}

	function dianji(a) {
	  var b = "fileElem_" + a;
	  gg = document.getElementById(b);
	  gg.click();

	}
	function handleFiles(obj,a) { 
        b = "file_" + a;
        c = "show_" + a;     
        f = "fileList_" + a;
        ff = "tdd_" + a;     
        tu = document.getElementById(b);
        show = document.getElementById(c);       

        addimg = document.getElementById(f);
        cha = document.getElementById(ff);  

        tu.style.display ='block';
        cha.style.display ='block';
        addimg.style.display='none';  
        window.URL = window.URL || window.webkitURL;    

        var files = obj.files,
            img = new Image();
        if(window.URL){
            //File API 
              img.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
              img.width = 200;
              img.onload = function(e) {
                 window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
              }                        

              show.appendChild(img);
        }else if(window.FileReader){
            //opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onload = function(e){
                alert(files[0].name + "," +e.total + " bytes");
                img.src = this.result;
                img.width = 200;
                
                show.appendChild(img); 
            }
        }else{
            //ie
            obj.select();
            obj.blur();
            var nfile = document.selection.createRange().text;
            document.selection.empty();
            img.src = nfile;
            img.width = 200;
            img.onload=function(){
              alert(nfile+","+img.fileSize + " bytes");
            }
            
            show.appendChild(img);      

            //fileList.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='image',src='"+nfile+"')";
        }
        $('#img_sign').val(1);
   }  