<?php /* Smarty version 2.6.18, created on 2009-09-10 09:56:22
         compiled from admin/gather_edit.html */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="css/main.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/show.css" rel="stylesheet" type="text/css" />
<link href="css/page.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div id="wrapper">
       
        <div id="show_list">
                     <h1><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/navigation.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></h1>
            <form action="index.php?action=gatherEditSave" method=post> 
	            <table  width="90%" cellspacing="10" cellpadding="0">
	              <tr>效果图：<p><img src="/upload/<?php echo $this->_tpl_vars['result']['image_url']; ?>
"><br /> </p></tr>
	              <tr>
	                <td>标题：
	                <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['result']['id']; ?>
"/><input type="hidden" name="http" value="<?php echo $this->_tpl_vars['http']; ?>
"/>
	                <input type="text" name="title" size="50"/><?php echo $this->_tpl_vars['result']['title']; ?>

	                </td>
	              </tr>
	              <tr>
	                <td>分类：
	                <select name="category_id" id="category_id">
	                <?php $_from = $this->_tpl_vars['category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p']):
?>
	                <option value="<?php echo $this->_tpl_vars['p']['id']; ?>
"><?php echo $this->_tpl_vars['p']['title']; ?>
</option>
	                <?php endforeach; endif; unset($_from); ?>
	                </select><?php echo $this->_tpl_vars['result']['content']; ?>
</td>
	              </tr>
	              <tr>
	                <td>关键字：<textarea type="text" style="width:460px;" name="keywords"></textarea><?php echo $this->_tpl_vars['result']['keywords']; ?>
</td>
	              </tr>
	              <tr>
	                <td>描&nbsp;&nbsp;&nbsp;述：<textarea type="text" style="width:460px;height:120px" name="description"></textarea><?php echo $this->_tpl_vars['result']['description']; ?>
</td>
	              </tr>
	              <tr>
	                <td>内&nbsp;&nbsp;&nbsp;容：<textarea type="text" style="width:460px;height:120px" name="content"></textarea><?php echo $this->_tpl_vars['result']['description']; ?>
</td>
	              </tr>
	              <tr>
	                <td>缩图地址：<input type="text" name="image_url" value="<?php echo $this->_tpl_vars['result']['image_url']; ?>
" size="50"/></td>
	              </tr>
	              <tr>
	                <td>下载地址：<input type="text" name="down_url" value="<?php echo $this->_tpl_vars['result']['down_url']; ?>
" size="50"/></td>
	              </tr>
	              <tr>
	                <td>重写地址：<input type="text" name="rewriter_url" value="<?php echo $this->_tpl_vars['result']['rewriter_url']; ?>
" size="50"/></td>
	              </tr>
	              <tr>
	                <td>素材来源：<input type="text" name="source" value="<?php echo $this->_tpl_vars['result']['gather_source']; ?>
" size="50"/></td>
	              </tr>
	              
	            </table>
	            <input type="submit" value="submit" />
            </form>
        </div>
</div>
