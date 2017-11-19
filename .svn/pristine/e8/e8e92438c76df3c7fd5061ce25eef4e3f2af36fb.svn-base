<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>信息管理中心</title>
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/reset.css">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/style.css?v=1.7">
	<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/agent.js"></script>
</head>
<body>
		<div class="material">
		<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="material_top">
			<div class="material_title"><?php echo ($vo["c_name"]); ?></div>
		</div>
		<div class="material_mid">
			<div class="fl"><input type="checkbox">&nbsp;全选</div>
			<div class="material_del fl" onclick="dwonall();">批量下载</div>
		</div>
		<div class="material_list">				
			<table>	
			<?php foreach ($vo['chidren'] as $key => $value) { ?>				
				<tr>
					<td width="30"><input type="checkbox" name="Id" value="<?php echo $value['c_id'] ?>"></td>
					<td><?php echo $value['c_name'] ?></td>
					<td><?php echo $value['c_addtime'] ?></td>
					<td>
						<a href="/wldApp/Agent.php/Shop/Material/downfile?Id=<?php echo $value['c_id'] ?>" title="下载">
							<img src="/wldApp/Agent/Shop/View/Public/images/index/down.png" alt="">
						</a>
					</td>
				</tr>
			<?php } ?>
			</table>				
		</div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>

    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script> 
    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/layer/1.9.3/layer.js"></script>
    <script type="text/javascript">
    	// 图片批量打包下载
    	function dwonall(tg) {
    		
    	}
    </script>
</body>
</html>